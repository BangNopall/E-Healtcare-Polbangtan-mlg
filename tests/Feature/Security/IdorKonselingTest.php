<?php

use App\Models\DataPsikolog;
use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\User;

test('mahasiswa tidak dapat melihat riwayat konsultasi psikologi mahasiswa lain (IDOR Protection)', function () {
    $victim = User::factory()->create(['role' => 'Mahasiswa']);
    $victim->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();

    $attacker = User::factory()->create(['role' => 'Mahasiswa']);
    $attacker->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();

    $konsultasiVictim = DataPsikolog::create([
        'user_id' => $victim->id,
        'tanggal' => now()->toDateString(),
        'keluhan' => 'Keluhan rahasia medis',
        'diagnosa' => 'Diagnosa rahasia',
        'metode_psikologi' => 'Metode A',
        'saran' => 'Saran rahasia',
    ]);

    $response = $this->actingAs($attacker)->get(route('user.konseling.detail-konsultasi', $konsultasiVictim->id));

    $response->assertForbidden();
});

test('mahasiswa tidak dapat membaca review feedback bimbingan mahasiswa lain (IDOR Protection)', function () {
    $victim = User::factory()->create(['role' => 'Mahasiswa']);
    $victim->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();
    $senso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);
    $attacker = User::factory()->create(['role' => 'Mahasiswa']);
    $attacker->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();

    $jadwal = JadwalBimbingan::create([
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Materi Rahasia',
        'token' => 'TOKEN123',
    ]);

    $feedbackVictim = FeedbackBimbingan::create([
        'jadwal_id' => $jadwal->id,
        'senso_id' => $senso->id,
        'siswa_id' => $victim->id,
        'feedback' => 'Feedback rahasia mahasiswa',
    ]);

    $response = $this->actingAs($attacker)->get(route('user.konseling.review-feedback-bimbingan', $feedbackVictim->id));

    $response->assertForbidden();
});

test('mahasiswa dapat membaca riwayat konsultasi miliknya sendiri', function () {
    $user = User::factory()->create(['role' => 'Mahasiswa']);
    $user->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();

    $konsultasi = DataPsikolog::create([
        'user_id' => $user->id,
        'tanggal' => now()->toDateString(),
        'keluhan' => 'Keluhan saya sendiri',
        'diagnosa' => 'Diagnosa saya sendiri',
        'metode_psikologi' => 'Metode B',
        'saran' => 'Saran saya sendiri',
    ]);

    $response = $this->actingAs($user)->get(route('user.konseling.detail-konsultasi', $konsultasi->id));

    $response->assertOk();
    $response->assertSee('Keluhan saya sendiri');
});
