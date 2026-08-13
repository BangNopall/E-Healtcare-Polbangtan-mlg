<?php

use App\Http\Controllers\API\InternalApiController;

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
    Route::get('/get_user', [InternalApiController::class, 'get_user'])->name('get_users');

    Route::get('/get_user_no_senso', [InternalApiController::class, 'userNoSenso'])->name('userNoSenso');
    Route::get('/get_user_bukan_senso_bukan_anak_asuh', [InternalApiController::class, 'userNoSensoNoAnakAsuh'])->name('userNoSensoNoAnakAsuh');

    Route::get('/get_konseling', [InternalApiController::class, 'getKonseling'])->name('getKonseling');

});


