<?php

use App\Models\Blok;
use App\Models\CDMI;
use App\Models\Prodi;
use App\Models\User;

test('syncs user nim from cdmi in chunks', function () {
    $blok = Blok::create(['name' => 'Blok A']);
    $prodi = Prodi::create(['name' => 'Penyuluhan Pertanian']);

    $users = [];
    for ($i = 1; $i <= 5; $i++) {
        $user = User::factory()->create([
            'role' => 'Mahasiswa',
            'nim' => null,
        ]);
        CDMI::create([
            'user_id' => $user->id,
            'nim' => '07.1.4.24.' . sprintf('%04d', $i),
            'prodi_id' => $prodi->id,
            'blok_id' => $blok->id,
            'no_ruangan' => 'A10' . $i,
        ]);
        $users[] = $user;
    }

    $this->artisan('app:sync-user-nim-from-cdmi')
        ->expectsOutputToContain('Selesai: 5 user diisi NIM-nya.')
        ->assertSuccessful();

    foreach ($users as $index => $user) {
        $fresh = $user->fresh();
        expect($fresh->nim)->toBe('07.1.4.24.' . sprintf('%04d', $index + 1));
    }
});

test('supports dry run mode without modifying users', function () {
    $blok = Blok::create(['name' => 'Blok B']);
    $prodi = Prodi::create(['name' => 'Peternakan']);

    $user = User::factory()->create([
        'role' => 'Mahasiswa',
        'nim' => null,
    ]);

    CDMI::create([
        'user_id' => $user->id,
        'nim' => '07.1.4.24.9999',
        'prodi_id' => $prodi->id,
        'blok_id' => $blok->id,
        'no_ruangan' => 'B201',
    ]);

    $this->artisan('app:sync-user-nim-from-cdmi', ['--dry-run' => true])
        ->expectsOutputToContain('Dry run — tidak ada perubahan yang ditulis ke database.')
        ->assertSuccessful();

    expect($user->fresh()->nim)->toBeNull();
});

test('is idempotent and skips users where nim is already matching', function () {
    $blok = Blok::create(['name' => 'Blok C']);
    $prodi = Prodi::create(['name' => 'Agribisnis']);

    $user = User::factory()->create([
        'role' => 'Mahasiswa',
        'nim' => '07.1.4.24.5555',
    ]);

    CDMI::create([
        'user_id' => $user->id,
        'nim' => '07.1.4.24.5555',
        'prodi_id' => $prodi->id,
        'blok_id' => $blok->id,
        'no_ruangan' => 'C301',
    ]);

    $this->artisan('app:sync-user-nim-from-cdmi')
        ->expectsOutputToContain('Selesai: 0 user diisi NIM-nya.')
        ->assertSuccessful();

    expect($user->fresh()->nim)->toBe('07.1.4.24.5555');
});
