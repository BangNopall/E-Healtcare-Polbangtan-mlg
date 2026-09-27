<?php

use App\Models\BimbinganSenso;
use App\Models\DataPsikolog;
use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\PresensiBimbingan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

test('prevents student from accessing form-feedback for unassigned senso (anti-idor)', function () {
    $student = User::factory()->create(['role' => 'Mahasiswa']);
    $student->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();

    $assignedSenso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);
    $unassignedSenso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);

    BimbinganSenso::create([
        'siswa_id' => $student->id,
        'senso_id' => $assignedSenso->id,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Materi Bimbingan',
        'token' => 'TOKEN_ABC_1',
    ]);

    $response = $this->actingAs($student)->get(route('user.konseling.form-feedback', [
        'id' => $unassignedSenso->id,
        'token' => $jadwal->token,
    ]));

    $response->assertForbidden();
});

test('prevents student from submitting feedback for unassigned senso (anti-idor)', function () {
    $student = User::factory()->create(['role' => 'Mahasiswa']);
    $student->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();

    $assignedSenso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);
    $unassignedSenso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);

    BimbinganSenso::create([
        'siswa_id' => $student->id,
        'senso_id' => $assignedSenso->id,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Materi Bimbingan',
        'token' => 'TOKEN_ABC_2',
    ]);

    // Unassigned senso is present, but NOT assigned to this student
    PresensiBimbingan::create([
        'jadwal_id' => $jadwal->id,
        'senso_id' => $unassignedSenso->id,
        'tanggal_presensi' => now()->toDateString(),
        'jam_presensi' => '08:05:00',
        'status' => 'Hadir',
    ]);

    $response = $this->actingAs($student)->post(route('user.konseling.store-feedback'), [
        'jadwal_id' => $jadwal->id,
        'senso_id' => $unassignedSenso->id,
        'siswa_id' => $student->id,
        'feedback' => 'Feedback untuk senso yang bukan pembimbingnya',
    ]);

    $response->assertForbidden();
    expect(FeedbackBimbingan::where('siswa_id', $student->id)->count())->toBe(0);
});

test('prevents feedback submission if senso was not present on that schedule', function () {
    $student = User::factory()->create(['role' => 'Mahasiswa']);
    $student->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();

    $assignedSenso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);

    BimbinganSenso::create([
        'siswa_id' => $student->id,
        'senso_id' => $assignedSenso->id,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Materi Bimbingan',
        'token' => 'TOKEN_ABC_3',
    ]);

    // Senso default status on creation is Alpha
    $response = $this->actingAs($student)->post(route('user.konseling.store-feedback'), [
        'jadwal_id' => $jadwal->id,
        'senso_id' => $assignedSenso->id,
        'siswa_id' => $student->id,
        'feedback' => 'Mencoba kirim feedback saat senso Alpha',
    ]);

    $response->assertSessionHas('error', 'Feedback belum dapat diisi karena pembimbing belum hadir pada jadwal ini.');
    expect(FeedbackBimbingan::where('siswa_id', $student->id)->count())->toBe(0);
});

test('allows feedback submission when senso is assigned and present', function () {
    $student = User::factory()->create(['role' => 'Mahasiswa']);
    $student->forceFill(['cdmi_complete' => 1, 'dmti_complete' => 1, 'is_email_changed' => 1, 'is_password_changed' => 1])->save();

    $assignedSenso = User::factory()->create(['role' => 'Mahasiswa', 'senso' => 1]);

    BimbinganSenso::create([
        'siswa_id' => $student->id,
        'senso_id' => $assignedSenso->id,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'materi' => 'Materi Bimbingan',
        'token' => 'TOKEN_ABC_4',
    ]);

    // Update senso attendance to Hadir
    PresensiBimbingan::where('jadwal_id', $jadwal->id)
        ->where('senso_id', $assignedSenso->id)
        ->update(['status' => 'Hadir', 'jam_presensi' => '08:05:00']);

    $response = $this->actingAs($student)->post(route('user.konseling.store-feedback'), [
        'jadwal_id' => $jadwal->id,
        'senso_id' => $assignedSenso->id,
        'siswa_id' => $student->id,
        'feedback' => 'Bimbingan sangat membantu dan bermanfaat.',
    ]);

    $response->assertRedirect(route('user.konseling.link-feedback'));
    $response->assertSessionHas('success', 'Feedback berhasil disimpan');

    expect(FeedbackBimbingan::where('siswa_id', $student->id)->count())->toBe(1);
});

test('soft deletes consultation record and records audit log instead of hard delete', function () {
    Log::spy();

    $admin = User::factory()->create(['role' => 'Admin']);
    $patient = User::factory()->create(['role' => 'Mahasiswa']);

    $consultation = DataPsikolog::create([
        'user_id' => $patient->id,
        'tanggal' => now()->toDateString(),
        'keluhan' => 'Keluhan pasien',
        'diagnosa' => 'Diagnosa stres akademik',
        'metode_psikologi' => 'Konseling kognitif',
        'saran' => 'Istirahat teratur',
    ]);

    $response = $this->actingAs($admin)->delete(route('konseling.deleteKonsultasi', $consultation->id));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Data Psikolog baru berhasil dihapus');

    // Assert it is soft-deleted (still exists in db, but trashed)
    $this->assertDatabaseHas('data_psikologs', [
        'id' => $consultation->id,
        'deleted_by' => $admin->id,
    ]);

    $freshConsultation = DataPsikolog::withTrashed()->find($consultation->id);
    expect($freshConsultation)->not->toBeNull();
    expect($freshConsultation->deleted_at)->not->toBeNull();
    expect(DataPsikolog::find($consultation->id))->toBeNull();

    Log::shouldHaveReceived('info')
        ->with('Medical record soft-deleted', \Mockery::on(function ($context) use ($admin, $consultation) {
            return (int) $context['by'] === (int) $admin->id &&
                   (int) $context['record_id'] === (int) $consultation->id;
        }));
});
