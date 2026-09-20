<?php

use App\Models\JadwalBimbingan;
use App\Models\PresensiBimbingan;
use App\Models\User;

test('presensi bimbingan dapat menyimpan status Terlambat tanpa error truncation skema', function () {
    $senso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);
    $jadwal = JadwalBimbingan::create([
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '09:00:00',
        'token' => 'TOKEN999',
    ]);

    $presensi = PresensiBimbingan::create([
        'jadwal_id' => $jadwal->id,
        'senso_id' => $senso->id,
        'tanggal_presensi' => now()->toDateString(),
        'status' => 'Terlambat',
    ]);

    $presensi->refresh();
    expect($presensi->status)->toBe('Terlambat');
});

test('user yang bukan senso diblokir saat mengakses qrcodebimbingan', function () {
    $nonSenso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 0,
    ]);
    $nonSenso->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();

    $response = $this->actingAs($nonSenso)->get(route('user.konseling.kodeqr-bimbingan'));

    $response->assertSessionHas('error', 'Anda Tidak Memiliki Akses Ke Halaman Ini');
});
