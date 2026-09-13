<?php

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;

beforeEach(function () {
    config(['sso.secret' => 'testing-shared-secret']);
    $this->withoutMiddleware(VerifyCsrfToken::class);
});

test('pejabat read-only dapat mengakses halaman dashboard dan list GET', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->get('/konseling');

    $response->assertOk();
});

test('pejabat read-only diizinkan menjalankan pencarian atau filter data POST', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->post('/lainnya/mahasiswa/filter', [
            'filter' => 'test',
            'prodi' => null,
        ]);

    // Filter endpoint returns 200 OK view with filtered data, not 403
    $response->assertOk();
});

test('pejabat read-only diblokir 403 saat mencoba membuat jadwal bimbingan POST', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->post('/konseling/jadwal-bimbingan/create', [
            'tanggal' => now()->format('Y-m-d'),
            'materi' => 'Materi Bimbingan',
        ]);

    $response->assertForbidden();
});

test('pejabat read-only diblokir 403 saat mencoba menghapus jadwal bimbingan DELETE', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->delete('/konseling/jadwal-bimbingan/delete/1');

    $response->assertForbidden();
});

test('pejabat read-only diblokir 403 saat mencoba membuat data mahasiswa POST', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->post('/lainnya/mahasiswa/store', [
            'name' => 'Siswa Baru',
            'email' => 'siswabaru@gmail.com',
            'nim' => '999888777',
        ]);

    $response->assertForbidden();
});

test('pejabat read-only diblokir 403 saat mencoba menghapus mahasiswa POST destroy', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->post('/lainnya/mahasiswa/1');

    $response->assertForbidden();
});

test('admin penuh tanpa flag readonly tidak diblokir oleh middleware readonly', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    // Admin hitting delete without read-only session:
    // might fail with 404 or redirect or whatever the controller does, but NEVER 403 from read-only enforcer
    $response = $this->actingAs($admin)
        ->delete('/konseling/jadwal-bimbingan/delete/999999');

    // Make sure it is not 403 Forbidden
    expect($response->status())->not->toBe(403);
});
