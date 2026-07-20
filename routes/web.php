<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrController;
use App\Http\Controllers\testController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KesehatanController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\InventarisAlatController;
use App\Http\Controllers\InventarisObatController;
use App\Http\Controllers\API\InternalApiController;
use App\Http\Controllers\SuratManagementController;
use App\Http\Controllers\PetugasManagementController;
use App\Http\Controllers\BimbinganKonselingController;
use App\Http\Controllers\KaryawanManagementController;
use App\Http\Controllers\User\KesehatanUserController;
use App\Http\Controllers\User\KonselingUserController;
use App\Http\Controllers\MahasiswaManagementController;
use App\Http\Controllers\InventarisConsumableController;

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
            Route::prefix('medical')->name('kesehatan.')->group(function () {
                // KesehatanUserController
                Route::get('/', [KesehatanUserController::class, 'dashboard'])->name('dashboard');
                // QrController
                Route::get('/kodeqr-kesehatan', [QrController::class, 'qrcode'])->name('kodeqr');

                Route::get('/riwayat-laporan', [KesehatanUserController::class, 'riwayatLaporan'])->name('riwayat-laporan');
                Route::get('/riwayat-laporan/surat-keterangan-berobat/{id}', [KesehatanUserController::class, 'showSuratKeteranganObat'])->name('surat-keterangan-berobat.show');
                Route::get('/riwayat-laporan/surat-keterangan-sehat/{id}', [KesehatanUserController::class, 'showSuratKeteranganSehat'])->name('surat-keterangan-sehat.show');
                Route::get('/riwayat-laporan/surat-keterangan-sakit/{id}', [KesehatanUserController::class, 'showSuratKeteranganSakit'])->name('surat-keterangan-sakit.show');
                Route::get('/riwayat-laporan/surat-rujukan/{id}', [KesehatanUserController::class, 'showSuratRujukan'])->name('surat-rujukan.show');

                Route::get('/print-laporan/surat-keterangan-berobat/{id}', [KesehatanUserController::class, 'printSuratKeteranganObat'])->name('surat-keterangan-berobat.print');
                Route::get('/print-laporan/surat-keterangan-sehat/{id}', [KesehatanUserController::class, 'printSuratKeteranganSehat'])->name('surat-keterangan-sehat.print');
                Route::get('/print-laporan/surat-keterangan-sakit/{id}', [KesehatanUserController::class, 'printSuratKeteranganSakit'])->name('surat-keterangan-sakit.print');
                Route::get('/print-laporan/surat-rujukan/{id}', [KesehatanUserController::class, 'printSuratRujukan'])->name('surat-rujukan.print');
            });
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
        // inventaris prefix route
        Route::prefix('inventaris')->name('inventaris.')->group(function () {
            Route::get('/', [DashboardController::class, 'inventaris'])->name('dashboard');
            Route::get('/pengaturan', [InventarisObatController::class, 'pengaturanInventaris'])->name('pengaturanInventaris');
            Route::post('/pengaturan/print', [InventarisObatController::class, 'printInventaris'])->name('printInventaris');
            // Kategori routes
            Route::prefix('kategori')->name('kategori.')->group(function () {
                Route::get('/obat/delete/{id}', [InventarisObatController::class, 'deleteKategori'])->name('obat.delete');
                Route::get('/alat/delete/{id}', [InventarisAlatController::class, 'deleteKategoriAlat'])->name('alat.delete');
                Route::get('/consumable/delete/{id}', [InventarisConsumableController::class, 'deleteKategoriTersisa'])->name('consumable.delete');

                Route::post('/obat/store', [InventarisObatController::class, 'storeKategori'])->name('obat.store');
                Route::post('/obat/update/{id}', [InventarisObatController::class, 'updateKategori'])->name('obat.update');

                Route::post('/alat/store', [InventarisAlatController::class, 'storeKategoriAlat'])->name('alat.store');
                Route::post('/alat/update/{id}', [InventarisAlatController::class, 'updateKategoriAlat'])->name('alat.update');

                Route::post('/consumable/store', [InventarisConsumableController::class, 'storeKategoriTersisa'])->name('consumable.store');
                Route::post('/consumable/update/{id}', [InventarisConsumableController::class, 'updateKategoriTersisa'])->name('consumable.update');
            });

            // Obat routes
            Route::prefix('obat')->name('obat.')->group(function () {
                Route::get('/', [InventarisObatController::class, 'index'])->name('index');
                Route::get('/{uuid}', [InventarisObatController::class, 'show'])->name('show');
                Route::get('/log/pengguna/{ulid}', [InventarisObatController::class, 'logPengguna'])->name('logpengguna');

                Route::post('/deposit/{uuid}', [InventarisObatController::class, 'deposit'])->name('deposit');
                Route::post('/store', [InventarisObatController::class, 'store'])->name('store');
                Route::post('/filter', [InventarisObatController::class, 'filter'])->name('filter');
                Route::post('/withdraw/{uuid}', [InventarisObatController::class, 'withdraw'])->name('withdraw');
                Route::post('/update-foto-obat/{uuid}', [InventarisObatController::class, 'updateFotoObat'])->name('update-foto');
                Route::post('/update/{uuid}', [InventarisObatController::class, 'update'])->name('update');

                Route::delete('/{uuid}', [InventarisObatController::class, 'destroy'])->name('destroy');
            });

            // Alat routes
            Route::prefix('alat')->name('alat.')->group(function () {
                Route::get('/', [InventarisAlatController::class, 'index'])->name('index');
                Route::get('/{uuid}', [InventarisAlatController::class, 'show'])->name('show');

                Route::post('/deposit/{uuid}', [InventarisAlatController::class, 'deposit'])->name('deposit');
                Route::post('/store', [InventarisAlatController::class, 'store'])->name('store');
                Route::post('/filter', [InventarisAlatController::class, 'filter'])->name('filter');
                Route::post('/withdraw/{uuid}', [InventarisAlatController::class, 'withdraw'])->name('withdraw');
                Route::post('/update-foto-alat/{uuid}', [InventarisAlatController::class, 'updateFotoAlat'])->name('update-foto');
                Route::post('/update/{uuid}', [InventarisAlatController::class, 'update'])->name('update');

                Route::delete('/{uuid}', [InventarisAlatController::class, 'destroy'])->name('destroy');
            });

            // Consumable routes
            Route::prefix('consumable')->name('consumable.')->group(function () {
                Route::get('/', [InventarisConsumableController::class, 'index'])->name('index');
                Route::get('/{uuid}', [InventarisConsumableController::class, 'show'])->name('show');

                Route::post('/deposit/{uuid}', [InventarisConsumableController::class, 'depositConsumable'])->name('deposit');
                Route::post('/store', [InventarisConsumableController::class, 'storeConsumable'])->name('store');
                Route::post('/filter', [InventarisConsumableController::class, 'filterConsumable'])->name('filter');
                Route::post('/withdraw/{uuid}', [InventarisConsumableController::class, 'withdrawConsumable'])->name('withdraw');
                Route::post('/update/{uuid}', [InventarisConsumableController::class, 'updateConsumable'])->name('update');
                Route::post('/update-foto-alat/{uuid}', [InventarisConsumableController::class, 'updateFotoConsumable'])->name('update-foto');

                Route::delete('/{uuid}', [InventarisConsumableController::class, 'destroy'])->name('destroy');
            });
        });
        // kesehatan prefix route
        Route::prefix('medical')->name('kesehatan.')->group(function () {
            // Kesehatan
            Route::get('/', [KesehatanController::class, 'dashboard'])->name('dashboard');
            Route::get('/kamera', [QrController::class, 'kamera'])->name('kamera');
            Route::post('/store-kamera', [QrController::class, 'scanQr'])->name('kamera.store');
            Route::get('/riwayat-surat', [SuratManagementController::class, 'riwayatLaporan'])->name('riwayat');
            Route::get('/export-rekammedis', [RekamMedisController::class, 'laporanRekamMedis'])->name('laporan-rekam-medis');
            Route::post('/print-rekammedis', [RekamMedisController::class, 'printLaporanRekamMedis'])->name('print.laporan-rekam-medis');
        
            // route request surat rujukan from konseling
            Route::get('request-rujukan-konseling', [SuratManagementController::class, 'requestSuratRujukanKonseling'])->name('request-rujukan-konseling');
            Route::get('request-rujukan-konseling/{id}/{requestId}', [SuratManagementController::class, 'detailKonsultasiFromMedical'])->name('detail-request-rujukan-konseling');
            Route::get('review-request-rujukan-konseling/{requestId}', [SuratManagementController::class, 'reviewRequestSuratRujukanKonseling'])->name('review-request-rujukan-konseling');
            Route::post('filter-request-rujukan-konseling', [SuratManagementController::class, 'filterRequestSuratRujukanKonseling'])->name('filter-request-rujukan-konseling');
            Route::post('reject-request-rujukan-konseling/{requestId}', [SuratManagementController::class, 'rejectRequestSuratRujukanKonseling'])->name('reject-request-rujukan-konseling');
            Route::patch('request-rujukan-konseling/{id}/{requestId}', [SuratManagementController::class, 'requestSuratRujukanKonselingStore'])->name('request-rujukan-konseling.store');
            // Route Baru untuk laporan keterangan
            Route::get('/form/riwayat-pasien/{id}', [RekamMedisController::class, 'formRiwayatPasien'])->name('riwayat-pasien');
            Route::get('/form/detail-rekam-medis/{id}', [RekamMedisController::class, 'detailRekamMedis'])->name('detailRekamMedis');
            Route::post('/form/riwayat-pasien/{id}', [RekamMedisController::class, 'storeRekamMedis'])->name('riwayat-pasien.store');
            Route::get('/form/riwayat-pasien/rekam-medis/{id}', [RekamMedisController::class, 'detailRekamMedis'])->name('riwayat-pasien.detail');
            Route::post('/form/riwayat-pasien/rekam-medis/{id}', [RekamMedisController::class, 'tipeSurat'])->name('riwayat-pasien.tipe-surat');
            // Download pdf rekam medis
            Route::get('/form/riwayat-pasien/rekam-medis/{id}/download', [RekamMedisController::class, 'downloadRekamMedis'])->name('riwayat-pasien.rekam-medis.download');
            
            // surat keterangan berobat
            Route::get('/form/riwayat-pasien/surat-keterangan-berobat/{id}', [SuratManagementController::class, 'suratKeteranganObat'])->name('form.surat-keterangan-obat');
            Route::patch('/form/store/surat-keterangan-berobat/{id}', [SuratManagementController::class, 'StoreSuratKeteranganObat'])->name('form.store.skb');
            Route::get('/form/riwayat-pasien/surat-keterangan-berobat/hasil/{id}', [SuratManagementController::class, 'reviewSuratKeteranganObat'])->name('lkb.hasil-surat');
            Route::get('/surat-keterangan-berobat/print/{id}', [SuratManagementController::class, 'printSuratKeteranganObat'])->name('form.surat-keterangan-berobat.print');
            Route::get('/riwayat-surat/surat-keterangan-berobat/{id}', [SuratManagementController::class, 'showSuratKeteranganObat'])->name('form.surat-keterangan-berobat.show');
            Route::delete('/form/surat-keterangan-berobat/{id}/delete', [SuratManagementController::class, 'deleteSuratKeteranganObat'])->name('form.surat-keterangan-berobat.delete');
            // surat keterangan sakit
            Route::get('/form/riwayat-pasien/surat-keterangan-sakit/{id}', [SuratManagementController::class, 'suratKeteranganSakit'])->name('form.surat-keterangan-sakit');
            Route::patch('/form/store/surat-keterangan-sakit/{id}', [SuratManagementController::class, 'StoreSuratKeteranganSakit'])->name('form.store.sks');
            Route::get('/form/riwayat-pasien/surat-keterangan-sakit/hasil/{id}', [SuratManagementController::class, 'reviewSuratKeteranganSakit'])->name('lks.hasil-surat');
            Route::get('/surat-keterangan-sakit/print/{id}', [SuratManagementController::class, 'printSuratKeteranganSakit'])->name('form.surat-keterangan-sakit.print');
            Route::get('/riwayat-surat/surat-keterangan-sakit/{id}', [SuratManagementController::class, 'showSuratKeteranganSakit'])->name('form.surat-keterangan-sakit.show');
            Route::delete('/form/surat-keterangan-sakit/{id}/delete', [SuratManagementController::class, 'deleteSuratKeteranganSakit'])->name('form.surat-keterangan-sakit.delete');
            // surat rujukan
            Route::get('/form/riwayat-pasien/surat-rujukan/{id}', [SuratManagementController::class, 'suratRujukan'])->name('form.surat-rujukan');
            Route::patch('/form/store/surat-rujukan/{id}', [SuratManagementController::class, 'StoreSuratRujukan'])->name('form.store.sr');
            Route::get('/form/riwayat-pasien/surat-rujukan/hasil/{id}', [SuratManagementController::class, 'reviewSuratRujukan'])->name('lkr.hasil-surat');
            Route::get('/surat-rujukan/print/{id}', [SuratManagementController::class, 'printSuratRujukan'])->name('form.surat-keterangan-rujukan.print');
            Route::get('/riwayat-surat/surat-rujukan/{id}', [SuratManagementController::class, 'showSuratRujukan'])->name('form.surat-rujukan.show');
            Route::delete('/form/surat-rujukan/{id}/delete', [SuratManagementController::class, 'deleteSuratRujukan'])->name('form.surat-rujukan.delete');
            // surat keterangan sehat
            Route::get('/form/riwayat-pasien/surat-keterangan-sehat/{id}', [SuratManagementController::class, 'suratKeteranganSehat'])->name('form.surat-keterangan-sehat');
            Route::patch('/form/store/surat-keterangan-sehat/{id}', [SuratManagementController::class, 'StoreSuratKeteranganSehat'])->name('form.store.skse');
            Route::get('/form/riwayat-pasien/surat-keterangan-sehat/hasil/{id}', [SuratManagementController::class, 'reviewSuratKeteranganSehat'])->name('lkse.hasil-surat');
            Route::get('/surat-keterangan-sehat/print/{id}', [SuratManagementController::class, 'printSuratKeteranganSehat'])->name('form.surat-keterangan-sehat.print');
            Route::get('/riwayat-surat/surat-keterangan-sehat/{id}', [SuratManagementController::class, 'showSuratKeteranganSehat'])->name('form.surat-keterangan-sehat.show');
            Route::delete('/form/surat-keterangan-sehat/{id}/delete', [SuratManagementController::class, 'deleteSuratKeteranganSehat'])->name('form.surat-keterangan-sehat.delete');

            Route::post('/filter-rm', [RekamMedisController::class, 'filterRm'])->name('filter.rm');
            Route::post('/filter-ks', [RekamMedisController::class, 'filterKs'])->name('filter.ks');

            // Route::view('/data-kontrol-pasien', 'kesehatan.data-kontrol-pasien')->name('data-kontrol-pasien');
            Route::get('/data-kontrol-pasien', [KesehatanController::class, 'dataKontrolPasien'])->name('data-kontrol-pasien');
            Route::post('/filter-dkp', [KesehatanController::class, 'filterDKP'])->name('filter-dkp');

        });
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

require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
