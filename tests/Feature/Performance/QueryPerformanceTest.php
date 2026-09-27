<?php

use App\Models\BimbinganSenso;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('endpoint userNoSensoNoAnakAsuh bebas dari N+1 query', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    // Buat 10 mahasiswa
    $users = User::factory()->count(10)->create([
        'role' => 'Mahasiswa',
        'senso' => 0,
    ]);

    // Jadikan 3 mahasiswa sebagai anak asuh
    $sensoUser = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);
    for ($i = 0; $i < 3; $i++) {
        BimbinganSenso::create([
            'siswa_id' => $users[$i]->id,
            'senso_id' => $sensoUser->id,
        ]);
    }

    DB::enableQueryLog();

    $response = $this->actingAs($admin)->get(route('api.userNoSensoNoAnakAsuh'));

    $response->assertOk();
    $queries = DB::getQueryLog();

    // Query count harus efisien (maksimal 2 kueri: sesi/user auth dan 1 kueri elokuen relasional)
    // Jangan sampai ada 10+ kueri loop N+1
    expect(count($queries))->toBeLessThanOrEqual(3);
});
