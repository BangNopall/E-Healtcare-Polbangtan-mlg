<?php

use App\Models\SsoTicket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Builds a signed SSO handoff query string, mirroring the canonical string
 * format documented in config/sso.php: "{nim}|{expires_at}|{nonce}".
 *
 * @param  array<string, mixed>  $overrides
 */
function buildSignedSsoUrl(array $overrides = []): string
{
    $params = array_merge([
        'nim' => '1234567890123456',
        'expires_at' => now()->addMinutes(5)->timestamp,
        'nonce' => (string) Str::uuid(),
    ], $overrides);

    if (! array_key_exists('signature', $overrides)) {
        $canonical = "{$params['nim']}|{$params['expires_at']}|{$params['nonce']}";
        $params['signature'] = hash_hmac('sha256', $canonical, config('sso.secret'));
    } else {
        $params['signature'] = $overrides['signature'];
    }

    return '/sso?'.http_build_query($params);
}

beforeEach(function () {
    config(['sso.secret' => 'testing-shared-secret']);
});

test('tolak jika signature tidak cocok dan tampilkan view error sso', function () {
    $url = buildSignedSsoUrl(['signature' => 'signature-yang-salah-total']);

    $response = $this->get($url);

    $response->assertForbidden();
    $response->assertViewIs('auth.sso-error');
    $response->assertSee('Validasi Keamanan Gagal');
    $response->assertSee('ERR_SSO_INVALID_SIGNATURE');
    $this->assertGuest();
});

test('tolak jika tiket sudah kedaluwarsa dan tampilkan view error sso', function () {
    // Signature dihitung ulang untuk expires_at ini (via buildSignedSsoUrl),
    // jadi kegagalan murni karena waktu, bukan karena signature salah.
    $url = buildSignedSsoUrl(['expires_at' => now()->subMinute()->timestamp]);

    $response = $this->get($url);

    $response->assertForbidden();
    $response->assertViewIs('auth.sso-error');
    $response->assertSee('Tiket Akses Kedaluwarsa');
    $response->assertSee('ERR_SSO_TICKET_EXPIRED');
    $this->assertGuest();
});

test('tolak jika nonce sudah pernah dipakai dan tampilkan view error sso', function () {
    $nonce = (string) Str::uuid();

    SsoTicket::create([
        'nonce' => $nonce,
        'nim' => '1234567890123456',
        'used_at' => now(),
    ]);

    $url = buildSignedSsoUrl(['nonce' => $nonce]);

    $response = $this->get($url);

    $response->assertForbidden();
    $response->assertViewIs('auth.sso-error');
    $response->assertSee('Tiket Sudah Pernah Digunakan');
    $response->assertSee('ERR_SSO_NONCE_REPLAYED');
    $this->assertGuest();
    // Nonce dipakai ulang tidak boleh membuat baris kedua.
    $this->assertDatabaseCount('sso_tickets', 1);
});

test('tolak dan tampilkan pesan ramah jika nim mahasiswa tidak ditemukan', function () {
    Log::spy();

    $usersBefore = User::count();

    $url = buildSignedSsoUrl(['nim' => '9999999999999999']);

    $response = $this->get($url);

    $response->assertForbidden();
    $response->assertViewIs('auth.sso-error');
    $response->assertSee('Data Mahasiswa Belum Terdaftar');
    $response->assertSee('9999999999999999');
    $response->assertSee('ERR_SSO_STUDENT_NOT_FOUND');
    $response->assertSee('Kembali ke E-Management Asrama');
    $this->assertGuest();
    // JANGAN buat user baru — jumlah baris users harus tetap sama.
    expect(User::count())->toBe($usersBefore);
    Log::shouldHaveReceived('warning')->once();
});

test('login dan redirect ke dashboard konseling saat semua validasi lolos', function () {
    $mahasiswa = User::factory()->create([
        'role' => 'Mahasiswa',
        'nim' => '1234567890123456',
    ]);

    $url = buildSignedSsoUrl(['nim' => $mahasiswa->nim]);

    $response = $this->get($url);

    $this->assertAuthenticatedAs($mahasiswa);
    $response->assertRedirect(route('user.konseling.dashboard'));
    $this->assertDatabaseCount('sso_tickets', 1);
});

function buildSignedSsoV2Url(array $overrides = []): string
{
    $params = array_merge([
        'identifier' => 'admin@polbangtanmalang.ac.id',
        'role' => 'admin',
        'expires_at' => now()->addMinutes(5)->timestamp,
        'nonce' => (string) Str::uuid(),
    ], $overrides);

    if (! array_key_exists('signature', $overrides)) {
        $canonical = "{$params['identifier']}|{$params['role']}|{$params['expires_at']}|{$params['nonce']}";
        $params['signature'] = hash_hmac('sha256', $canonical, config('sso.secret'));
    } else {
        $params['signature'] = $overrides['signature'];
    }

    return '/sso?'.http_build_query($params);
}

test('admin sso login dan redirect ke dashboard konseling admin', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Test',
        'email' => 'admin@polbangtanmalang.ac.id',
        'role' => 'Admin',
    ]);

    $url = buildSignedSsoV2Url([
        'identifier' => $admin->email,
        'role' => 'admin',
    ]);

    $response = $this->get($url);

    $this->assertAuthenticatedAs($admin);
    $response->assertRedirect(route('konseling.dashboard'));
    $this->assertDatabaseCount('sso_tickets', 1);
    expect(session('sso_readonly'))->toBeNull();
});

test('tolak dan tampilkan pesan ramah jika akun admin tidak ditemukan', function () {
    Log::spy();

    $url = buildSignedSsoV2Url([
        'identifier' => 'admin.unknown@polbangtanmalang.ac.id',
        'role' => 'admin',
    ]);

    $response = $this->get($url);

    $response->assertForbidden();
    $response->assertViewIs('auth.sso-error');
    $response->assertSee('Akun Administrator Belum Terdaftar');
    $response->assertSee('admin.unknown@polbangtanmalang.ac.id');
    $response->assertSee('ERR_SSO_ADMIN_NOT_FOUND');
    $this->assertGuest();
    Log::shouldHaveReceived('critical')->once();
});

test('pejabat sso login sebagai admin dengan flag readonly dan redirect ke dashboard konseling', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Klinik',
        'email' => 'admin@polbangtanmalang.ac.id',
        'role' => 'Admin',
    ]);

    $url = buildSignedSsoV2Url([
        'identifier' => 'kaprodi@polbangtanmalang.ac.id',
        'role' => 'pejabat',
        'name' => 'Bapak Kaprodi',
    ]);

    $response = $this->get($url);

    $this->assertAuthenticatedAs($admin);
    $response->assertRedirect(route('konseling.dashboard'));
    $this->assertDatabaseCount('sso_tickets', 1);
    expect(session('sso_readonly'))->toBeTrue();
    expect(session('sso_pejabat_name'))->toBe('Bapak Kaprodi');
    expect(session('sso_pejabat_email'))->toBe('kaprodi@polbangtanmalang.ac.id');
});

test('tolak dan tampilkan pesan ramah untuk pejabat jika host admin klinik belum ada', function () {
    Log::spy();

    // Pastikan tidak ada akun Admin sama sekali di database
    User::where('role', 'Admin')->delete();

    $url = buildSignedSsoV2Url([
        'identifier' => 'kaprodi@polbangtanmalang.ac.id',
        'role' => 'pejabat',
        'name' => 'Bapak Kaprodi',
    ]);

    $response = $this->get($url);

    $response->assertForbidden();
    $response->assertViewIs('auth.sso-error');
    $response->assertSee('Layanan Pejabat Belum Dapat Diakses');
    $response->assertSee('Bapak Kaprodi');
    $response->assertSee('ERR_SSO_PEJABAT_HOST_NOT_FOUND');
    $this->assertGuest();
    Log::shouldHaveReceived('critical')->once();
});

test('tolak jika signature v2 tidak cocok dan tampilkan view error sso', function () {
    $url = buildSignedSsoV2Url([
        'signature' => 'invalid-signature-v2',
    ]);

    $response = $this->get($url);

    $response->assertForbidden();
    $response->assertViewIs('auth.sso-error');
    $response->assertSee('Validasi Keamanan Gagal');
    $response->assertSee('ERR_SSO_INVALID_SIGNATURE');
    $this->assertGuest();
});
