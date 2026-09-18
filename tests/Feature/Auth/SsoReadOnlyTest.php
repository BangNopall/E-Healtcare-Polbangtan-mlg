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

test('pejabat read-only dialihkan saat mencoba mengakses halaman profile', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->get('/profile');

    $response->assertRedirect(route('konseling.dashboard'));
    $response->assertSessionHas('error');
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

test('pejabat read-only dialihkan kembali dengan pesan error saat mutasi via form web non-ajax', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->from('/konseling/jadwal-bimbingan')
        ->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->post('/konseling/jadwal-bimbingan/create', [
            'tanggal' => now()->format('Y-m-d'),
            'materi' => 'Materi Bimbingan',
        ]);

    $response->assertRedirect('/konseling/jadwal-bimbingan');
    $response->assertSessionHas('error');
});

test('pejabat read-only diblokir 403 json saat mutasi via ajax', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->postJson('/konseling/jadwal-bimbingan/create', [
            'tanggal' => now()->format('Y-m-d'),
            'materi' => 'Materi Bimbingan',
        ]);

    $response->assertForbidden();
    $response->assertJson([
        'message' => 'Akses ditolak: Akun Pejabat hanya memiliki izin Read-Only pada sistem E-Klinik.',
    ]);
});

test('pejabat read-only dialihkan kembali dengan pesan error saat menghapus data via form non-ajax', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->from('/konseling/jadwal-bimbingan')
        ->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->delete('/konseling/jadwal-bimbingan/delete/1');

    $response->assertRedirect('/konseling/jadwal-bimbingan');
    $response->assertSessionHas('error');
});

test('pejabat read-only diblokir 403 json saat menghapus data via ajax', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['sso_readonly' => true, 'sso_role' => 'Pejabat'])
        ->deleteJson('/konseling/jadwal-bimbingan/delete/1');

    $response->assertForbidden();
    $response->assertJson([
        'message' => 'Akses ditolak: Akun Pejabat hanya memiliki izin Read-Only pada sistem E-Klinik.',
    ]);
});

test('admin penuh tanpa flag readonly tidak diblokir oleh middleware readonly', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->delete('/konseling/jadwal-bimbingan/delete/999999');

    expect($response->status())->not->toBe(403);
});

test('login manual admin membersihkan flag sso_readonly', function () {
    $admin = User::factory()->create([
        'email' => 'admin.login@polbangtanmalang.ac.id',
        'password' => bcrypt('password123'),
        'role' => 'Admin',
    ]);

    $response = $this->withSession([
        'sso_readonly' => true,
        'sso_role' => 'Pejabat',
        'sso_pejabat_name' => 'Bapak Kaprodi',
    ])->post('/login', [
        'login' => $admin->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect();
    expect(session('sso_readonly'))->toBeNull();
    expect(session('sso_role'))->toBeNull();
    expect(session('sso_pejabat_name'))->toBeNull();
});
