<?php

use App\Models\User;
use Illuminate\Support\Str;

test('tolak sso jika SSO_SHARED_SECRET kosong atau terlalu pendek', function () {
    config(['sso.secret' => '']); // empty secret

    $params = [
        'nim' => '1234567890123456',
        'expires_at' => now()->addMinutes(1)->timestamp,
        'nonce' => (string) Str::uuid(),
    ];
    $canonical = "{$params['nim']}|{$params['expires_at']}|{$params['nonce']}";
    $params['signature'] = hash_hmac('sha256', $canonical, '');

    $url = '/sso?' . http_build_query($params);
    $response = $this->get($url);

    $response->assertForbidden();
    $this->assertGuest();
});

test('tolak sso jika expires_at disetel melebihi batas toleransi wajar (unbounded TTL)', function () {
    config(['sso.secret' => 'valid-very-long-secret-key-that-has-at-least-32-chars-long!']);

    $params = [
        'nim' => '1234567890123456',
        'expires_at' => now()->addDays(30)->timestamp, // 30 days into future
        'nonce' => (string) Str::uuid(),
    ];
    $canonical = "{$params['nim']}|{$params['expires_at']}|{$params['nonce']}";
    $params['signature'] = hash_hmac('sha256', $canonical, config('sso.secret'));

    $url = '/sso?' . http_build_query($params);
    $response = $this->get($url);

    $response->assertForbidden();
    $this->assertGuest();
});

test('rejects admin sso when specific admin email does not exist without fallback to other admins', function () {
    config(['sso.secret' => 'valid-very-long-secret-key-that-has-at-least-32-chars-long!']);

    // Create an existing admin in database
    $existingAdmin = User::factory()->create([
        'name' => 'Existing Admin',
        'email' => 'admin.real@polbangtanmalang.ac.id',
        'role' => 'Admin',
    ]);

    // Attack / misconfiguration: SSO request for a non-existent admin email
    $params = [
        'identifier' => 'admin.nonexistent@polbangtanmalang.ac.id',
        'role' => 'admin',
        'expires_at' => now()->addMinutes(5)->timestamp,
        'nonce' => (string) Str::uuid(),
    ];
    $canonical = "{$params['identifier']}|{$params['role']}|{$params['expires_at']}|{$params['nonce']}";
    $params['signature'] = hash_hmac('sha256', $canonical, config('sso.secret'));

    $url = '/sso?' . http_build_query($params);
    $response = $this->get($url);

    $response->assertForbidden();
    $response->assertViewIs('auth.sso-error');
    $response->assertSee('ERR_SSO_ADMIN_NOT_FOUND');
    $this->assertGuest();
});

test('prunes expired sso tickets older than 24 hours via artisan command', function () {
    $oldTicket = \App\Models\SsoTicket::create([
        'nonce' => 'old-ticket-nonce-1',
        'used_at' => now()->subHours(48),
    ]);
    \Illuminate\Support\Facades\DB::table('sso_tickets')
        ->where('id', $oldTicket->id)
        ->update([
            'created_at' => now()->subHours(48),
            'used_at' => now()->subHours(48),
        ]);

    \App\Models\SsoTicket::create([
        'nonce' => 'recent-ticket-nonce-2',
        'used_at' => now()->subHours(2),
    ]);

    $this->artisan('sso:prune-tickets')
        ->expectsOutputToContain('Selesai membersihkan tiket SSO')
        ->assertSuccessful();

    expect(\App\Models\SsoTicket::where('nonce', 'old-ticket-nonce-1')->exists())->toBeFalse();
    expect(\App\Models\SsoTicket::where('nonce', 'recent-ticket-nonce-2')->exists())->toBeTrue();
});

