<?php

use App\Http\Controllers\QrController;
use App\Models\JadwalBimbingan;
use App\Models\PresensiBimbingan;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-09-27 08:10:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

test('kamera bimbingan hanya dapat diakses oleh Admin atau Psikolog', function () {
    $mahasiswa = User::factory()->create(['role' => 'Mahasiswa']);
    $mahasiswa->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();
    $admin = User::factory()->create(['role' => 'Admin']);
    $psikolog = User::factory()->create(['role' => 'Psikolog']);

    JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Umum',
    ]);

    // Mahasiswa harus diblokir (403 Forbidden)
    $this->actingAs($mahasiswa)
        ->get(route('konseling.kamera-bimbingan'))
        ->assertForbidden();

    // Admin dan Psikolog diizinkan
    $this->actingAs($admin)
        ->get(route('konseling.kamera-bimbingan'))
        ->assertOk();

    $this->actingAs($psikolog)
        ->get(route('konseling.kamera-bimbingan'))
        ->assertOk();
});

test('petugas menerima konfirmasi presensi informatif menyertakan nama senso saat memindai qr bimbingan', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $senso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 1,
        'name' => 'Ahmad Pembimbing',
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Kedisiplinan',
    ]);

    $response = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $senso->bimbingan_token,
    ]);

    $response->assertSessionHas('success');
    $successMessage = session('success');
    expect($successMessage)->toContain('Ahmad Pembimbing');
    expect($successMessage)->toContain('Hadir');

    $presensi = PresensiBimbingan::where('senso_id', $senso->id)
        ->where('jadwal_id', $jadwal->id)
        ->first();

    expect($presensi->status)->toBe('Hadir');
});

test('presensi bimbingan mengevaluasi keterlambatan dengan batas toleransi 15 menit menggunakan Carbon', function () {
    Carbon::setTestNow('2026-09-27 08:16:00'); // 1 menit setelah batas grace period 08:15:00

    $admin = User::factory()->create(['role' => 'Admin']);
    $senso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 1,
        'name' => 'Budi Pembimbing',
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Karakter',
    ]);

    $response = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $senso->bimbingan_token,
    ]);

    $response->assertSessionHas('success');
    $successMessage = session('success');
    expect($successMessage)->toContain('Budi Pembimbing');
    expect($successMessage)->toContain('Terlambat');

    $presensi = PresensiBimbingan::where('senso_id', $senso->id)
        ->where('jadwal_id', $jadwal->id)
        ->first();

    expect($presensi->status)->toBe('Terlambat');
});

test('presensi bimbingan menolak pemindaian setelah jam sesi bimbingan berakhir', function () {
    Carbon::setTestNow('2026-09-27 12:05:00'); // Lewat jam selesai 12:00:00

    $admin = User::factory()->create(['role' => 'Admin']);
    $senso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 1,
    ]);

    JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Selesai',
    ]);

    $response = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $senso->bimbingan_token,
    ]);

    $response->assertSessionHas('error', 'Sesi bimbingan hari ini telah berakhir.');
});

test('dead methods kamera dan scanQr sudah dieliminasi dari QrController', function () {
    $reflector = new ReflectionClass(QrController::class);

    expect($reflector->hasMethod('kamera'))->toBeFalse();
    expect($reflector->hasMethod('scanQr'))->toBeFalse();
});
