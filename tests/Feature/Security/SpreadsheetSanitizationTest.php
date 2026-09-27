<?php

use App\Exports\laporanBimbingan;
use App\Exports\laporanKonsultasi;
use App\Exports\laporanRM;
use App\Helpers\SecurityHelper;
use App\Models\User;
use App\Models\DataPsikolog;
use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;

test('security helper sanitizes formula triggers by prepending single quote', function () {
    expect(SecurityHelper::sanitizeSpreadsheetCell('=1+1'))->toBe("'=1+1");
    expect(SecurityHelper::sanitizeSpreadsheetCell('+628123456'))->toBe("'+628123456");
    expect(SecurityHelper::sanitizeSpreadsheetCell('-500'))->toBe("'-500");
    expect(SecurityHelper::sanitizeSpreadsheetCell('@admin'))->toBe("'@admin");
    expect(SecurityHelper::sanitizeSpreadsheetCell("\tmalicious"))->toBe("'\tmalicious");
    expect(SecurityHelper::sanitizeSpreadsheetCell("Normal Text"))->toBe("Normal Text");
    expect(SecurityHelper::sanitizeSpreadsheetCell(null))->toBe("");
});

test('laporan konsultasi excel sanitizes NIM and clinical notes from formula injection', function () {
    $student = User::factory()->create([
        'name' => '=HYPERLINK("http://evil.com")',
        'nim' => '+12345678',
        'role' => 'Mahasiswa',
    ]);
    $student->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
    ])->save();

    $konsul = DataPsikolog::create([
        'user_id' => $student->id,
        'tanggal' => now()->toDateString(),
        'keluhan' => '@testKeluhan',
        'diagnosa' => '-testDiagnosa',
        'metode_psikologi' => '+metode',
    ]);

    $export = new laporanKonsultasi(collect([$konsul->load(['user.getCDMI'])]), 'September');
    $view = $export->view();
    $html = html_entity_decode($view->render(), ENT_QUOTES);

    // Pastikan NIM disanitasi (terdapat single quote di depan karakter formula)
    expect($html)->toContain("'+12345678");
    expect($html)->toContain("'=HYPERLINK");
    expect($html)->toContain("'@testKeluhan");
    expect($html)->toContain("'-testDiagnosa");
});

test('laporan feedback excel sanitizes NIM and feedback from formula injection', function () {
    $student = User::factory()->create([
        'name' => 'Budi',
        'nim' => '=999888',
        'role' => 'Mahasiswa',
    ]);
    $student->forceFill([
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
    ])->save();

    $senso = User::factory()->create([
        'name' => 'Senso Pembimbing',
        'role' => 'Mahasiswa',
        'senso' => 1,
    ]);

    $jadwal = JadwalBimbingan::create([
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'token' => 'TOKENSPREADSHEET',
        'materi' => '=cmd|calc',
    ]);

    $feedback = FeedbackBimbingan::create([
        'jadwal_id' => $jadwal->id,
        'senso_id' => $senso->id,
        'siswa_id' => $student->id,
        'feedback' => '@feedbackFormula',
    ]);

    $export = new laporanBimbingan(collect([$feedback->load(['jadwal', 'senso', 'siswa.getCDMI'])]), 'September');
    $view = $export->view();
    $html = html_entity_decode($view->render(), ENT_QUOTES);

    expect($html)->toContain("'=999888");
    expect($html)->toContain("'=cmd|calc");
    expect($html)->toContain("'@feedbackFormula");
});

test('laporanRM excel view exists and renders without exception', function () {
    $export = new laporanRM(collect([]), 'September');
    $view = $export->view();
    $html = $view->render();

    expect($html)->toContain('LAPORAN REKAM MEDIS');
});
