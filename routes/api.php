<?php

use App\Http\Controllers\API\InternalApiController;
use App\Http\Controllers\InventarisObatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('role:Admin,Developer,Dokter,Psikolog,Perawat')->name('api.')->group(function () {
    Route::get('/daftar-alat', [InternalApiController::class, 'getDaftarAlat'])->name('getDaftarAlat');
    Route::get('/kategori_obat', [InternalApiController::class, 'getKategoriObat'])->name('getKategoriObat');
    Route::get('/daftar-obat', [InternalApiController::class, 'getDaftarObat'])->name('getDaftarObat');
    Route::get('/get_user', [InternalApiController::class, 'get_user'])->name('get_users');
    Route::get('/kategori_alat', [InternalApiController::class, 'getKategoriAlat'])->name('getKategoriAlat');
    Route::get('/kategori_consumable', [InternalApiController::class, 'getKategoriConsumables'])->name('getKategoriConsumables');
    Route::get('/daftar_rumah_sakit', [InternalApiController::class, 'daftarRS'])->name('daftarRS');

    Route::get('/get_user_no_senso', [InternalApiController::class, 'userNoSenso'])->name('userNoSenso');
    Route::get('/get_user_bukan_senso_bukan_anak_asuh', [InternalApiController::class, 'userNoSensoNoAnakAsuh'])->name('userNoSensoNoAnakAsuh');

    Route::get('/get_data_obat_bulan', [InternalApiController::class, 'getDataObatBulan'])->name('getDataObatBulan');
    Route::get('/get_data_alat_bulan', [InternalApiController::class, 'getDataAlatBulan'])->name('getDataAlatBulan');
    Route::get('/get_data_consumable_bulan', [InternalApiController::class, 'getDataConsumableBulan'])->name('getDataConsumableBulan');

    Route::get('/get_data_obat_ringkas', [InternalApiController::class, 'getDataObatRingkas'])->name('getDataObatRingkas');
    Route::get('/get_data_alat_ringkas', [InternalApiController::class, 'getDataAlatRingkas'])->name('getDataAlatRingkas');
    Route::get('/get_data_consumable_ringkas', [InternalApiController::class, 'getDataConsumableRingkas'])->name('getDataConsumableRingkas');

    Route::get('/get_sk', [InternalApiController::class, 'getSk'])->name('getSk');
    Route::get('/get_penyakit', [InternalApiController::class, 'getPenyakit'])->name('getPenyakit');

    Route::get('/get_konseling', [InternalApiController::class, 'getKonseling'])->name('getKonseling');

});

Route::middleware('role:Mahasiswa,Karyawan')->name('api.')->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/get_surat_user', [InternalApiController::class, 'getSuratUser'])->name('getSuratUser');
    });
});
