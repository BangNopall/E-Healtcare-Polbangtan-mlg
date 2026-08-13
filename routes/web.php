<?php

use App\Http\Controllers\Auth\SsoLoginController;
use App\Http\Controllers\BimbinganKonselingController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\KaryawanManagementController;
use App\Http\Controllers\MahasiswaManagementController;
use App\Http\Controllers\PetugasManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\testController;
use App\Http\Controllers\User\KonselingUserController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Peran Pengguna dan Hak Akses
|--------------------------------------------------------------------------
|
| Middleware untuk mengatur peran dan hak akses pengguna:
| - 'role:Admin'
| - 'role:Karyawan'
| - 'role:Dokter'
| - 'role:Psikolog,Perawat'
| - 'role:Mahasiswa'
|
| Middleware 'cdmi' memeriksa kelengkapan CDMI.
| Middleware 'dmti' memeriksa kelengkapan DMTI.
|
*/

// Route::view('/form/riwayat-pasien', 'kesehatan.form.riwayat-pasien')->name('riwayat-pasien');
// Route::view('/form/detail-rekam-medis', 'kesehatan.form.detail-rm')->name('detail-rm');
// keterangan berobat
// Route::view('/form/laporan-keterangan-berobat/buat-surat', 'kesehatan.form.laporan-keterangan-berobat.buat-surat')->name('lkb.buat-surat');
// Route::view('/form/laporan-keterangan-berobat/hasil-surat', 'kesehatan.form.laporan-keterangan-berobat.hasil-surat')->name('lkb.hasil-surat');

// Route::view('/form/laporan-keterangan-sakit/buat-surat', 'kesehatan.form.laporan-keterangan-sakit.buat-surat')->name('lks.buat-surat');
// Route::view('/form/laporan-keterangan-sakit/hasil-surat', 'kesehatan.form.laporan-keterangan-sakit.hasil-surat')->name('lks.hasil-surat');

// Route::view('/form/laporan-keterangan-rujukan/buat-surat', 'kesehatan.form.laporan-keterangan-rujukan.buat-surat')->name('lkr.buat-surat');
// Route::view('/form/laporan-keterangan-rujukan/hasil-surat', 'kesehatan.form.laporan-keterangan-rujukan.hasil-surat')->name('lkr.hasil-surat');

// Route::view('/form/laporan-keterangan-sehat/buat-surat', 'kesehatan.form.laporan-keterangan-sehat.buat-surat')->name('lkse.buat-surat');
// Route::view('/form/laporan-keterangan-sehat/hasil-surat', 'kesehatan.form.laporan-keterangan-sehat.hasil-surat')->name('lkse.hasil-surat');

// Route::view('/riwayat-kontrol', 'kesehatan.riwayat-kontrol')->name('riwayat-kontrol');

Route::get('/test', [testController::class, 'testprint']);

// Endpoint publik penerima handoff SSO dari E-Management. Sengaja di luar
// grup middleware 'guest'/'auth' — harus bisa diakses baik oleh browser
// yang belum punya sesi maupun yang sudah (login akan menimpa sesi lama).
Route::get('/sso', [SsoLoginController::class, 'receive'])->name('sso.login');

Route::middleware('guest')->group(function () {
    Route::redirect('/', '/login');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('/', '/profile');
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');

        Route::post('/update-avatar/{id}', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
        Route::post('/create-rpd/{id}', [ProfileController::class, 'storeRPD'])->name('create-rpd');
        Route::patch('/update-dmti/{id}', [ProfileController::class, 'updateDMTI'])->name('update-dmti');
        Route::patch('/update-cdmi/{id}', [ProfileController::class, 'updateCDMI'])->name('update-cdmi');
    });

    // User routes ( Mahasiswa dan karyawan )
    Route::middleware('role:Mahasiswa,Karyawan')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {

        });
    });

    // Mahasiswa routes KONSELING
    Route::middleware('role:Mahasiswa')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::prefix('konseling')->name('konseling.')->group(function () {
                Route::view('/', 'konseling.user.dashboard')->name('dashboard');

                Route::get('/kodeqr-bimbingan', [QrController::class, 'qrcodebimbingan'])->name('kodeqr-bimbingan');
                Route::get('/link-feedback', [KonselingUserController::class, 'linkfeedback'])->name('link-feedback');
                Route::get('/form-feedback/{id}/{token}', [KonselingUserController::class, 'formfeedback'])->name('form-feedback');
                Route::post('/form-feedback/store', [KonselingUserController::class, 'storefeedback'])->name('store-feedback');
                Route::get('/review-feedback-bimbingan/{id}', [KonselingUserController::class, 'reviewfeedback'])->name('review-feedback-bimbingan');

                Route::get('/kodeqr-konsultasi', [QrController::class, 'qrcodekonsultasi'])->name('kodeqr-konsultasi');
                Route::get('riwayat-konsultasi', [KonselingUserController::class, 'riwayatKonsultasi'])->name('riwayat-konsultasi');
                Route::get('riwayat-konsultasi/{id}', [KonselingUserController::class, 'detailKonsultasi'])->name('detail-konsultasi');
                Route::post('riwayat-konsultasi/filter', [KonselingUserController::class, 'filterKonsultasi'])->name('filter-konsultasi');
            });
        });
    });

    Route::middleware('role:Admin,Dokter,Psikolog,Perawat')->group(function () {

        // konseling prefix route
        Route::prefix('konseling')->name('konseling.')->group(function () {
            Route::get('/', [DashboardController::class, 'konseling'])->name('dashboard');
            Route::get('/data-sensuh', [BimbinganKonselingController::class, 'dataSenso'])->name('data-senso');
            Route::get('/data-sensuh/show/{id}', [BimbinganKonselingController::class, 'detailSenso'])->name('detail-data-senso');
            Route::get('jadwal-bimbingan', [BimbinganKonselingController::class, 'jadwalBimbingan'])->name('jadwal-bimbingan');
            Route::get('kamera-bimbingan', [QrController::class, 'kameraBimbingan'])->name('kamera-bimbingan');
            Route::get('riwayat-feedback', [BimbinganKonselingController::class, 'riwayatFeedback'])->name('riwayat-feedback');
            Route::get('riwayat-feedback/{id}', [BimbinganKonselingController::class, 'showFeedback'])->name('detail-feedback');
            Route::get('/kamera-konsultasi', [QrController::class, 'kameraKonsultasi'])->name('kamera-konsultasi');
            Route::get('/form/konsultasi/{token}', [BimbinganKonselingController::class, 'formKonsultasi'])->name('form-konsultasi');
            Route::get('/form/review/konsultasi/{id}', [BimbinganKonselingController::class, 'reviewKonsultasi'])->name('review-konsultasi');
            Route::get('riwayat-konsultasi', [BimbinganKonselingController::class, 'riwayatKonsultasi'])->name('riwayat-konsultasi');
            Route::get('riwayat-konsultasi/{id}', [BimbinganKonselingController::class, 'detailKonsultasi'])->name('detail-konsultasi');
            Route::get('hasil-surat-rujukan', [BimbinganKonselingController::class, 'hasilSuratRujukan'])->name('hasil-surat-rujukan');
            Route::get('hasil-surat-rujukan/{requestId}', [BimbinganKonselingController::class, 'detailSuratRujukan'])->name('detail-surat-rujukan');

            Route::post('riwayat-konsultasi/filter', [BimbinganKonselingController::class, 'filterKonsultasi'])->name('filter-konsultasi');
            Route::post('request-surat-rujukan/{id}', [BimbinganKonselingController::class, 'requestSuratRujukan'])->name('request-surat-rujukan');
            Route::post('store/konsultasi/{id}', [BimbinganKonselingController::class, 'storeKonsultasi'])->name('storeKonsultasi');
            Route::post('store/kamera-konsultasi', [QrController::class, 'storeKameraKonsultasi'])->name('storeKameraKonsultasi');
            Route::post('store/kamera-bimbingan', [QrController::class, 'storeKameraBimbingan'])->name('storeKameraBimbingan');
            Route::post('/data-senso/filter', [BimbinganKonselingController::class, 'filterSenso'])->name('filter-senso');
            Route::post('/jadwal-bimbingan/filter', [BimbinganKonselingController::class, 'filterJadwalBimbingan'])->name('filterJadwalBimbingan');
            Route::post('/data-senso/create', [BimbinganKonselingController::class, 'createSenso'])->name('create-senso');
            Route::post('/jadwal-bimbingan/create', [BimbinganKonselingController::class, 'createJadwalBimbingan'])->name('createJadwalBimbingan');
            Route::post('/data-senso/create-anak-senso/{senso_id}', [BimbinganKonselingController::class, 'daftarsiswaAsuh'])->name('daftarsiswaAsuh');
            Route::post('/riwayat-feedback/filter', [BimbinganKonselingController::class, 'filterFeedback'])->name('filter-feedback');

            Route::delete('/delete/riwayat-konsultasi/{id}', [BimbinganKonselingController::class, 'deleteKonsultasi'])->name('deleteKonsultasi');
            Route::delete('/jadwal-bimbingan/delete/{id}', [BimbinganKonselingController::class, 'destroyJadwalBimbingan'])->name('destroyJadwalBimbingan');
            Route::delete('/data-senso/{id}', [BimbinganKonselingController::class, 'deleteSenso'])->name('delete-senso');
            Route::delete('/data-senso/hapus-anak-senso/{siswa_id}', [BimbinganKonselingController::class, 'deleteSiswaAsuh'])->name('deleteSiswaAsuh');
            Route::delete('/hapus-feedback/{id}', [BimbinganKonselingController::class, 'deleteFeedback'])->name('delete-feedback');

            Route::get('/export-konseling', [BimbinganKonselingController::class, 'laporanKonseling'])->name('laporan-konseling');
            Route::post('/print-konsultasi', [BimbinganKonselingController::class, 'printLaporanKonsultasi'])->name('print.laporan-konsultasi');
            Route::post('/print-feedback', [BimbinganKonselingController::class, 'printLaporanFeedback'])->name('print.laporan-feedback');

        });
    });
    Route::middleware('role:Admin')->group(function () {
        // Lainnya
        Route::prefix('lainnya')->name('lainnya.')->group(function () {
            Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
                Route::get('/', [MahasiswaManagementController::class, 'index'])->name('index');
                Route::post('/filter', [MahasiswaManagementController::class, 'filterMahasiswa'])->name('filter');

                Route::get('/{id}', [MahasiswaManagementController::class, 'show'])->name('show');
                Route::post('/store', [MahasiswaManagementController::class, 'store'])->name('store');
                Route::post('/{id}', [MahasiswaManagementController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/update-foto', [MahasiswaManagementController::class, 'updateAvatar'])->name('update-foto');
                Route::post('/{id}/hapus-foto', [MahasiswaManagementController::class, 'hapusAvatar'])->name('hapus-foto');
                Route::patch('/{id}/update-data-mahasiswa', [MahasiswaManagementController::class, 'updateDataMahasiswa'])->name('update-data-mahasiswa');
                Route::patch('/{id}/update-data-dmti', [MahasiswaManagementController::class, 'updateDataDMTI'])->name('update-data-dmti');
                Route::put('/{id}/update-password', [MahasiswaManagementController::class, 'updateDataPassword'])->name('update-data-password');
            });

            Route::prefix('karyawan')->name('karyawan.')->group(function () {
                Route::get('/', [KaryawanManagementController::class, 'index'])->name('index');
                Route::post('/filter', [KaryawanManagementController::class, 'filterKaryawan'])->name('filter');
                Route::get('/{id}', [KaryawanManagementController::class, 'show'])->name('show');
                Route::post('/store', [KaryawanManagementController::class, 'store'])->name('store');
            });

            Route::prefix('petugas')->name('petugas.')->group(function () {
                Route::get('/', [PetugasManagementController::class, 'index'])->name('index');
                Route::post('/filter', [PetugasManagementController::class, 'filterPetugas'])->name('filter');
                Route::get('/{id}', [PetugasManagementController::class, 'show'])->name('show');
                Route::post('/store', [PetugasManagementController::class, 'store'])->name('store');
                Route::post('/{id}', [PetugasManagementController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/update-foto', [PetugasManagementController::class, 'updateAvatar'])->name('update-foto');
                Route::post('/{id}/hapus-foto', [PetugasManagementController::class, 'hapusAvatar'])->name('hapus-foto');
                Route::patch('/{id}/update-data-mahasiswa', [PetugasManagementController::class, 'updateDataPetugas'])->name('update-data-petugas');
                Route::put('/{id}/update-password', [PetugasManagementController::class, 'updateDataPassword'])->name('update-data-password');
            });
        });
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
