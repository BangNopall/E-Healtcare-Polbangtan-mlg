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

test('tolak jika signature tidak cocok', function () {
    $url = buildSignedSsoUrl(['signature' => 'signature-yang-salah-total']);

    $response = $this->get($url);

    $response->assertForbidden();
    $this->assertGuest();
});

test('tolak jika tiket sudah kedaluwarsa', function () {
    // Signature dihitung ulang untuk expires_at ini (via buildSignedSsoUrl),
    // jadi kegagalan murni karena waktu, bukan karena signature salah.
    $url = buildSignedSsoUrl(['expires_at' => now()->subMinute()->timestamp]);

    $response = $this->get($url);

    $response->assertForbidden();
    $this->assertGuest();
});

test('tolak jika nonce sudah pernah dipakai', function () {
    $nonce = (string) Str::uuid();

    // Percobaan pertama: nonce baru, tapi nim tidak akan ditemukan di DB
    // (fokus test ini murni pada nonce, bukan resolusi user) — jadi
    // simpan tiketnya langsung, meniru hasil dari request pertama yang
    // sempat lolos pemeriksaan nonce sebelum gagal di langkah berikutnya.
    SsoTicket::create([
        'nonce' => $nonce,
        'nim' => '1234567890123456',
        'used_at' => now(),
    ]);

    $url = buildSignedSsoUrl(['nonce' => $nonce]);

    $response = $this->get($url);

    $response->assertForbidden();
    $this->assertGuest();
    // Nonce dipakai ulang tidak boleh membuat baris kedua.
    $this->assertDatabaseCount('sso_tickets', 1);
});

test('tolak dan tidak membuat user baru jika nim tidak ditemukan', function () {
    Log::spy();

    $usersBefore = User::count();

    $url = buildSignedSsoUrl(['nim' => '9999999999999999']);

    $response = $this->get($url);

    $response->assertForbidden();
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
