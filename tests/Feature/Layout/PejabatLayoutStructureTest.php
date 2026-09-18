<?php

use App\Models\User;

test('pejabat dashboard html tidak mengandung tag profile-dropdown yang rusak', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession([
            'sso_readonly' => true,
            'sso_role' => 'Pejabat',
            'sso_pejabat_name' => 'Dr. Pejabat Pengawas',
        ])
        ->get('/konseling');

    $response->assertOk();
    $html = $response->getContent();

    // Pastikan tidak ada tag <div pembuka yang tidak tertutup > sebelum tag <div anak
    // Pola error yang terjadi sebelumnya: dark:bg-dark"\s*<div
    expect($html)->not->toMatch('/dark:bg-dark"\s*<div/i');

    // Pastikan atribut role="menu" dan tag penutup '>' terpasang dengan benar pada dropdown
    expect($html)->toMatch('/dark:bg-dark"\s*role="menu"/i');
});

test('backdrop panel layout memiliki atribut x-cloak untuk mencegah flash overlay', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession([
            'sso_readonly' => true,
            'sso_role' => 'Pejabat',
        ])
        ->get('/konseling');

    $response->assertOk();
    $html = $response->getContent();

    // Verifikasi view setting-panel backdrop memiliki x-cloak
    expect($html)->toMatch('/<div[^>]*\bx-cloak\b[^>]*x-show="isSettingsPanelOpen"|<div[^>]*x-show="isSettingsPanelOpen"[^>]*\bx-cloak\b/i');

    // Verifikasi notification-panel backdrop memiliki x-cloak
    expect($html)->toMatch('/<div[^>]*\bx-cloak\b[^>]*x-show="isNotificationsPanelOpen"|<div[^>]*x-show="isNotificationsPanelOpen"[^>]*\bx-cloak\b/i');

    // Verifikasi search-panel backdrop memiliki x-cloak
    expect($html)->toMatch('/<div[^>]*\bx-cloak\b[^>]*x-show="isSearchPanelOpen"|<div[^>]*x-show="isSearchPanelOpen"[^>]*\bx-cloak\b/i');
});

test('tabel presensi konseling tidak menempatkan div langsung di dalam table', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->withSession([
            'sso_readonly' => true,
            'sso_role' => 'Pejabat',
        ])
        ->get('/konseling');

    $response->assertOk();
    $html = $response->getContent();

    // Browser parsing error: <div> directly inside <table>
    expect($html)->not->toMatch('/<table[^>]*>\s*<div/i');
});
