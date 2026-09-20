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
