<?php

use App\Models\User;
use App\Models\DataPsikolog;
use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('streams pdf export directly without leaving temporary files on public disk', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ]);

    $student = User::factory()->create([
        'role' => 'Mahasiswa',
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
    ]);

    DataPsikolog::create([
        'user_id' => $student->id,
        'tanggal' => now()->toDateString(),
        'keluhan' => 'Keluhan uji coba privasi',
        'diagnosa' => 'Diagnosa uji coba',
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'token' => 'TOKENJADWALTEST',
        'materi' => 'Materi Bimbingan',
    ]);

    FeedbackBimbingan::create([
        'jadwal_id' => $jadwal->id,
        'senso_id' => $admin->id,
        'siswa_id' => $student->id,
        'feedback' => 'Feedback positif',
    ]);

    // Test print konsultasi PDF
    $responseKonsultasi = $this->actingAs($admin)->post(route('konseling.print.laporan-konsultasi'), [
        'bulan' => now()->format('Y-m'),
        'submit' => 'pdf',
    ]);

    $responseKonsultasi->assertOk();
    expect($responseKonsultasi->headers->get('content-type'))->toBe('application/pdf');

    // Pastikan tidak ada file yang disimpan ke storage public
    expect(file_exists(storage_path('app/public/Laporan Konsultasi_' . now()->translatedFormat('F') . '.pdf')))->toBeFalse();
    expect(file_exists(storage_path('app/public/Laporan_Konsultasi_' . now()->translatedFormat('F') . '.pdf')))->toBeFalse();

    // Test print feedback PDF
    $responseFeedback = $this->actingAs($admin)->post(route('konseling.print.laporan-feedback'), [
        'bulan' => now()->format('Y-m'),
        'submit' => 'pdf',
    ]);

    $responseFeedback->assertOk();
    expect($responseFeedback->headers->get('content-type'))->toBe('application/pdf');

    expect(file_exists(storage_path('app/public/Laporan Bimbingan_' . now()->translatedFormat('F') . '.pdf')))->toBeFalse();
    expect(file_exists(storage_path('app/public/Laporan_Bimbingan_' . now()->translatedFormat('F') . '.pdf')))->toBeFalse();
});

test('blocks unauthorized users from downloading medical consultation pdf', function () {
    $student = User::factory()->create([
        'role' => 'Mahasiswa',
    ]);
    $student->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();

    $response = $this->actingAs($student)->post(route('konseling.print.laporan-konsultasi'), [
        'bulan' => now()->format('Y-m'),
        'submit' => 'pdf',
    ]);

    $response->assertForbidden();
});
