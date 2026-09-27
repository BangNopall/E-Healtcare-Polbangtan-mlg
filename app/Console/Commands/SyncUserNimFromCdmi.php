<?php

namespace App\Console\Commands;

use App\Models\CDMI;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:sync-user-nim-from-cdmi {--dry-run : Preview the sync without writing any changes}')]
#[Description('Backfill users.nim from c_d_m_i_s.nim (one row per user, matched by user_id)')]
class SyncUserNimFromCdmi extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $toFill = [];       // [user_id => nim] that will change users.nim
        $alreadyOk = 0;     // users.nim already matches — no-op, keeps this idempotent
        $failures = [];     // [user_id => reason]
        $totalCdmiUsers = 0;
        $seenUserIds = [];

        // Preload existing nim values once so the "already used by another user"
        // check below is an in-memory lookup, not one query per candidate.
        $existingNimOwners = User::query()
            ->whereNotNull('nim')
            ->pluck('id', 'nim');

        CDMI::query()
            ->select(['id', 'user_id', 'nim'])
            ->orderBy('id')
            ->chunkById(200, function ($rows) use (&$toFill, &$alreadyOk, &$failures, &$existingNimOwners, &$seenUserIds, &$totalCdmiUsers) {
                foreach ($rows as $row) {
                    $userId = $row->user_id;
                    $nim = $row->nim;

                    if (isset($seenUserIds[$userId])) {
                        if ($seenUserIds[$userId] !== $nim) {
                            $failures[$userId] = sprintf(
                                'User memiliki baris CDMI dengan NIM berbeda (%s vs %s) — ambigu, dilewati',
                                $seenUserIds[$userId],
                                $nim
                            );
                            unset($toFill[$userId]);
                        }
                        continue;
                    }

                    $seenUserIds[$userId] = $nim;
                    $totalCdmiUsers++;

                    if ($nim === null || trim((string) $nim) === '') {
                        $failures[$userId] = 'Kolom nim pada c_d_m_i_s kosong/null';
                        continue;
                    }

                    $user = User::find($userId);

                    if (! $user) {
                        // Defensive only: c_d_m_i_s.user_id has a FK constraint with
                        // cascade delete, so this should be unreachable in practice.
                        $failures[$userId] = 'Baris CDMI menunjuk ke user_id yang tidak ada';
                        continue;
                    }

                    if ($user->nim === $nim) {
                        $alreadyOk++;
                        continue;
                    }

                    $ownerOfNim = $existingNimOwners->get($nim);
                    if ($ownerOfNim !== null && $ownerOfNim !== $userId) {
                        $failures[$userId] = sprintf(
                            'NIM %s sudah dipakai user lain (id=%d) — kemungkinan data users.nim sudah diisi manual atau NIM CDMI keliru',
                            $nim,
                            $ownerOfNim
                        );
                        continue;
                    }

                    $toFill[$userId] = $nim;
                }
            });

        $this->reportSummary($isDryRun, count($toFill), $alreadyOk, $failures, $totalCdmiUsers);

        if ($isDryRun) {
            $this->line('');
            $this->comment('Dry run — tidak ada perubahan yang ditulis ke database.');

            return self::SUCCESS;
        }

        if ($toFill !== []) {
            DB::transaction(function () use ($toFill): void {
                foreach (array_chunk($toFill, 200, true) as $chunk) {
                    foreach ($chunk as $userId => $nim) {
                        User::whereKey($userId)->update(['nim' => $nim]);
                    }
                }
            });
        }

        $this->line('');
        $this->info(sprintf('Selesai: %d user diisi NIM-nya.', count($toFill)));

        return $failures === [] ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @param  array<int, string>  $failures
     */
    private function reportSummary(bool $isDryRun, int $willFillCount, int $alreadyOkCount, array $failures, int $totalCdmiUsers): void
    {
        $this->info($isDryRun ? 'Dry run: sinkronisasi users.nim dari c_d_m_i_s' : 'Sinkronisasi users.nim dari c_d_m_i_s');
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total user dengan baris CDMI', $totalCdmiUsers],
                ['Akan terisi / terisi', $willFillCount],
                ['Sudah sesuai (idempoten, tidak berubah)', $alreadyOkCount],
                ['Gagal', count($failures)],
            ]
        );

        if ($failures === []) {
            return;
        }

        $this->line('');
        $this->error('Detail kegagalan:');
        $this->table(
            ['User ID', 'Alasan'],
            collect($failures)
                ->map(fn (string $reason, int $userId) => [$userId, $reason])
                ->values()
                ->all()
        );
    }
}
