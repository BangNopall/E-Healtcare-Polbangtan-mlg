<?php

namespace Database\Seeders;

use App\Models\AlatLog;
use Carbon\Carbon;
use App\Models\InventoryAlat;
use App\Models\InventoryLog;
use App\Models\User;
use DateTime;
use Illuminate\Database\Seeder;

class InventoryAlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alats = [
            'meja', 'kursi', 'buku', 'pensil', 'penghapus', 'penggaris', 'lemari',
            'komputer', 'laptop', 'printer', 'kertas', 'spidol', 'papan tulis',
            'kalkulator', 'stopwatch', 'jam dinding', 'kursi roda', 'tongkat',
            'tongkat jalan', 'tongkat narsis', 'tongkat selfie', 'tongkat monopod',
            'tongkat tripod', 'tongkat tongsis', 'tongkat tongsos', 'tongkat tongtis',
            'tongkat tongtos', 'tongkat tongtus', 'tongkat tongtys', 'tongkat tongtis',
        ];

        foreach ($alats as $alat) {
            $firstDay = Carbon::now()->startOfMonth()->day;
            $lastDay = Carbon::now()->endOfMonth()->day;
            $this->CreateAlat($alat, $firstDay, $lastDay);

            $getAlatFromInventory = InventoryAlat::where('nama_alat', $alat)->first()->id;

            $randomDepositLooping = rand(1, 10);
            for ($i = 0; $i < $randomDepositLooping; $i++) {
                $this->DepositAlat($alat, $getAlatFromInventory);
            }

            $this->WithdrawAlat($alat, $getAlatFromInventory);
        }
    }

    public function CreateAlat($alat, $firstDay, $lastDay)
    {
        $randomDay = rand($firstDay, $lastDay);
        $getDay = Carbon::now()->subDays($randomDay);

        $adminUsers = User::where('role', 'Admin')->get();
        if ($adminUsers->isEmpty()) {
            throw new \Exception('No Admin users found.');
        }
        $idAdmin = $adminUsers->random()->id;

        InventoryAlat::factory()->create([
            'nama_alat' => $alat,
            'kode_alat' => InventoryAlat::factory()->uniqueKodeAlat($alat),
            'createdBy' => $idAdmin,
        ]);
        AlatLog::create([
            'alat_id' => InventoryAlat::where('nama_alat', $alat)->first()->id,
            'type' => 'created',
            'Qty' => 0,
            'description' => 'Create ' . $alat . ' By Seeder',
            'user_id' => $idAdmin,
            'date' => $getDay,
            'time' => $getDay,
        ]);
    }

    public function DepositAlat($alat, $getAlatFromInventory)
    {
        $adminUsers = User::where('role', 'Admin')->get();
        if ($adminUsers->isEmpty()) {
            throw new \Exception('No Admin users found.');
        }
        $idAdmin = $adminUsers->random()->id;

        $randomAddDays = rand(1, 7);
        $randomDeposit = rand(10, 500);
        InventoryAlat::where('nama_alat', $alat)->first()->update([
            'stok' => $randomDeposit,
        ]);
        $randDate = new DateTime();
        $randDate->setTime(mt_rand(0, 23), mt_rand(0, 59));
        AlatLog::create([
            'alat_id' => $getAlatFromInventory,
            'type' => 'Deposit',
            'Qty' => $randomDeposit,
            'description' => 'Create By Seeder',
            'user_id' => $idAdmin,
            'date' => now()->subDays($randomAddDays),
            'time' => $randDate->format('H:i:s'),
        ]);
    }

    public function WithdrawAlat($alat, $getAlatFromInventory)
    {
        $dokterUsers = User::where('role', 'Dokter')->get();
        if ($dokterUsers->isEmpty()) {
            throw new \Exception('No Dokter users found.');
        }
        $idDokter = $dokterUsers->random()->id;

        $dataAlat = InventoryAlat::where('nama_alat', $alat)->first();
        $stokAlat = $dataAlat->stok;
        while ($stokAlat > 0) {
            $randomWithdraw = rand(1, min($stokAlat, 10));
            $stokAlat -= $randomWithdraw;

            InventoryAlat::where('nama_alat', $alat)->first()->update([
                'stok' => $stokAlat,
            ]);

            $randDate = new DateTime();
            $randDate->setTime(mt_rand(0, 23), mt_rand(0, 59));
            AlatLog::create([
                'alat_id' => $getAlatFromInventory,
                'type' => 'Withdraw',
                'Qty' => $randomWithdraw,
                'description' => 'WD By Seeder',
                'user_id' => $idDokter,
                'date' => now(),
                'time' => $randDate->format('H:i:s'),
            ]);
        }
    }
}
