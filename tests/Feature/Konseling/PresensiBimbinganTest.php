<?php

use App\Models\JadwalBimbingan;
use App\Models\PresensiBimbingan;
use App\Models\User;
use Illuminate\Support\Carbon;

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

test('marks attendance as Hadir when scanned within grace period of 15 minutes', function () {
    Carbon::setTestNow('2026-09-25 08:10:00');

    $admin = User::factory()->create(['role' => 'Admin']);
    $senso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 1,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-25',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Bimbingan Kedisiplinan',
    ]);

    $response = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $senso->bimbingan_token,
    ]);

    $response->assertSessionHas('success');

    $presensi = PresensiBimbingan::where('senso_id', $senso->id)
        ->where('jadwal_id', $jadwal->id)
        ->first();

    expect($presensi->status)->toBe('Hadir');

    Carbon::setTestNow();
});

test('marks attendance as Terlambat when scanned after grace period but before end time', function () {
    Carbon::setTestNow('2026-09-25 08:30:00');

    $admin = User::factory()->create(['role' => 'Admin']);
    $senso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 1,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-25',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Bimbingan Karakter',
    ]);

    $response = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $senso->bimbingan_token,
    ]);

    $response->assertSessionHas('success');

    $presensi = PresensiBimbingan::where('senso_id', $senso->id)
        ->where('jadwal_id', $jadwal->id)
        ->first();

    expect($presensi->status)->toBe('Terlambat');

    Carbon::setTestNow();
});

test('rejects attendance scan when schedule has ended', function () {
    Carbon::setTestNow('2026-09-25 10:15:00');

    $admin = User::factory()->create(['role' => 'Admin']);
    $senso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 1,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-25',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Bimbingan Karakter',
    ]);

    $response = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $senso->bimbingan_token,
    ]);

    $response->assertSessionHas('error');

    $presensi = PresensiBimbingan::where('senso_id', $senso->id)
        ->where('jadwal_id', $jadwal->id)
        ->first();

    expect($presensi->status)->toBe('Alpha');

    Carbon::setTestNow();
});

test('does not overwrite existing Izin or Sakit status on qr scan', function () {
    Carbon::setTestNow('2026-09-25 08:05:00');

    $admin = User::factory()->create(['role' => 'Admin']);
    $senso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 1,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-25',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Bimbingan',
    ]);

    $presensi = PresensiBimbingan::where('senso_id', $senso->id)
        ->where('jadwal_id', $jadwal->id)
        ->first();

    // Senso sebelumnya izin
    $presensi->update(['status' => 'Izin']);

    $response = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $senso->bimbingan_token,
    ]);

    $response->assertSessionHas('error');

    $presensi->refresh();
    expect($presensi->status)->toBe('Izin');

    Carbon::setTestNow();
});

test('invalidates and regenerates bimbingan token immediately after successful scan', function () {
    Carbon::setTestNow('2026-09-25 08:05:00');

    $admin = User::factory()->create(['role' => 'Admin']);
    $senso = User::factory()->create([
        'role' => 'Mahasiswa',
        'senso' => 1,
    ]);

    $oldToken = $senso->bimbingan_token;

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-25',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Bimbingan',
    ]);

    $response1 = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $oldToken,
    ]);
    $response1->assertSessionHas('success');

    // Token user sudah di-regenerate
    $senso->refresh();
    expect($senso->bimbingan_token)->not->toBe($oldToken);

    // Pemindaian kedua dengan token lama harus ditolak
    $response2 = $this->actingAs($admin)->post(route('konseling.storeKameraBimbingan'), [
        'token' => $oldToken,
    ]);
    $response2->assertSessionHas('error');

    Carbon::setTestNow();
});
