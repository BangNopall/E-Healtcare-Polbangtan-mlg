<?php

use App\Models\BimbinganSenso;
use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\PresensiBimbingan;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-09-27 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

test('link feedback terbaru ditampilkan di tabel dan tombol form feedback muncul saat senso Hadir', function () {
    $senso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1, 'name' => 'Senso Pembimbing A']);
    $siswa = User::factory()->create(['role' => 'Mahasiswa', 'name' => 'Siswa B']);

    $siswa->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();

    BimbinganSenso::create([
        'siswa_id' => $siswa->id,
        'senso_id' => $senso->id,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Akademik & Mental',
        'token' => 'SCHEDULETOKEN123',
    ]);

    $presensi = PresensiBimbingan::where('jadwal_id', $jadwal->id)
        ->where('senso_id', $senso->id)
        ->first();

    $presensi->update([
        'tanggal_presensi' => '2026-09-27',
        'jam_presensi' => '08:10:00',
        'status' => 'Hadir',
    ]);

    $response = $this->actingAs($siswa)->get(route('user.konseling.link-feedback'));

    $response->assertOk();
    // Memastikan baris form feedback aktif ter-render dengan link ke form feedback dan materi bimbingan
    $response->assertSee('Bimbingan Akademik & Mental');
    $response->assertSee('Senso Pembimbing A');
    $expectedFormUrl = route('user.konseling.form-feedback', ['id' => $senso->id, 'token' => $jadwal->token]);
    $response->assertSee($expectedFormUrl, false);
    $response->assertSee('icon-[clarity--form-line]', false);
});

test('link feedback terbaru ditampilkan di tabel saat senso Terlambat', function () {
    $senso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1, 'name' => 'Senso Pembimbing B']);
    $siswa = User::factory()->create(['role' => 'Mahasiswa', 'name' => 'Siswa C']);

    $siswa->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();

    BimbinganSenso::create([
        'siswa_id' => $siswa->id,
        'senso_id' => $senso->id,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Kedisiplinan',
        'token' => 'SCHEDULETOKEN456',
    ]);

    $presensi = PresensiBimbingan::where('jadwal_id', $jadwal->id)
        ->where('senso_id', $senso->id)
        ->first();

    $presensi->update([
        'tanggal_presensi' => '2026-09-27',
        'jam_presensi' => '08:35:00',
        'status' => 'Terlambat',
    ]);

    $response = $this->actingAs($siswa)->get(route('user.konseling.link-feedback'));

    $response->assertOk();
    $response->assertSee('Bimbingan Kedisiplinan');
    $response->assertSee('icon-[clarity--form-line]', false);
});

test('menampilkan banner edukatif ketika pembimbing belum melakukan presensi', function () {
    $senso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1, 'name' => 'Senso Pembimbing C']);
    $siswa = User::factory()->create(['role' => 'Mahasiswa', 'name' => 'Siswa D']);

    $siswa->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();

    BimbinganSenso::create([
        'siswa_id' => $siswa->id,
        'senso_id' => $senso->id,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Karakter',
        'token' => 'SCHEDULETOKEN789',
    ]);

    // Status presensi senso masih Alpha (belum scan QR)
    $response = $this->actingAs($siswa)->get(route('user.konseling.link-feedback'));

    $response->assertOk();
    $response->assertDontSee('icon-[clarity--form-line]');
    $response->assertSee('belum melakukan presensi');
});

test('menampilkan pesan informatif ketika belum ada jadwal bimbingan hari ini', function () {
    $senso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);
    $siswa = User::factory()->create(['role' => 'Mahasiswa']);

    $siswa->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();

    BimbinganSenso::create([
        'siswa_id' => $siswa->id,
        'senso_id' => $senso->id,
    ]);

    // Tidak ada jadwal dibuat untuk hari ini
    $response = $this->actingAs($siswa)->get(route('user.konseling.link-feedback'));

    $response->assertOk();
    $response->assertSee('Tidak ada jadwal bimbingan');
});

test('menampilkan pesan informatif ketika mahasiswa belum dipasangkan dengan pembimbing', function () {
    $siswa = User::factory()->create(['role' => 'Mahasiswa']);

    $siswa->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();

    JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Terbuka',
    ]);

    $response = $this->actingAs($siswa)->get(route('user.konseling.link-feedback'));

    $response->assertOk();
    $response->assertSee('belum dipasangkan dengan pembimbing');
});

test('menampilkan tanda selesai jika mahasiswa sudah mengirim feedback untuk jadwal aktif', function () {
    $senso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1, 'name' => 'Senso Pembimbing D']);
    $siswa = User::factory()->create(['role' => 'Mahasiswa']);

    $siswa->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ])->save();

    BimbinganSenso::create([
        'siswa_id' => $siswa->id,
        'senso_id' => $senso->id,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => '2026-09-27',
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '12:00:00',
        'materi' => 'Bimbingan Kebangsaan',
        'token' => 'SCHEDULETOKEN999',
    ]);

    $presensi = PresensiBimbingan::where('jadwal_id', $jadwal->id)
        ->where('senso_id', $senso->id)
        ->first();

    $presensi->update([
        'tanggal_presensi' => '2026-09-27',
        'jam_presensi' => '08:10:00',
        'status' => 'Hadir',
    ]);

    FeedbackBimbingan::create([
        'jadwal_id' => $jadwal->id,
        'senso_id' => $senso->id,
        'siswa_id' => $siswa->id,
        'feedback' => 'Sesi bimbingan sangat bermanfaat.',
    ]);

    $response = $this->actingAs($siswa)->get(route('user.konseling.link-feedback'));

    $response->assertOk();
    $response->assertSee('Anda telah mengisi feedback');
});
