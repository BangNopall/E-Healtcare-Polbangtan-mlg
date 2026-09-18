<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\JadwalBimbingan;
use App\Models\PresensiBimbingan;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class QrController extends Controller
{
    public function qrcode()
    {
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        if ($user->dmti_complete == 0) {
            return redirect()->route('profile.edit')->with('error', 'Silahkan lengkapi Data Anda Terlebih Dahulu');
        }

        if (empty($user->kesehatan_token)) {
            return back()->with('error', 'Anda Tidak Memiliki Token Silakan hubungi Petugas Untuk Generate Token');
        }


        try {
            $validatedQR = [
                'token' => $user->kesehatan_token,
            ];

            $json = json_encode($validatedQR);
            $QrCode = QrCode::size(300)->generate($json);

            $data['QrCode'] = $QrCode;
            $data['user'] = $user;

            // if ($user->kesehatan_token_expired_at < now()) {
            //     User::UpdateTokenKesehatan($user->id);
            // }

            return view('kesehatan.user.kodeqr', $data)->with('success', 'Token Anda Hanya Berlaku Selama 60 Menit.');
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Anda Tidak Memiliki Token Silakan hubungi Petugas Untuk Generate Token');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Token gagal ditampilkan: ' . $th->getMessage());
                return back()->with('error', 'Terjadi kesalahan, silakan coba lagi atau hubungi petugas.');
            }
        }
    }

    public function kamera()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return back()->with('error', 'User tidak ditemukan ketika sqan QR');
            }

            $data['user'] = $user;

            return view('kesehatan.kamera', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'User tidak ditemukan ketika sqan QR');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('User tidak ditemukan ketika sqan QR: ' . $th->getMessage());
                return back()->with('error', 'User tidak ditemukan ketika sqan QR.');
            }
        }
    }

    public function scanQr(Request $request)
    {

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Token tidak valid');
        }

        $token = $request->token;

        // dd($token);

        try {
            $user = User::where('kesehatan_token', $token)->firstOrFail();

            if ($user->kesehatan_token_expired_at < now()) {
                User::UpdateTokenKesehatan($user->id);
                return back()->with('error', 'Token Anda Sudah Kadaluarsa Kami baru saja memperbarui Token Anda Silahkan coba lagi. REFRESH HALAMAN ANDA');
            }

            if (!$user) {
                return back()->with('error', 'User Tidak Ditemukan Silahkan coba lagi.');
            }

            return redirect()->route('kesehatan.riwayat-pasien', $user->id);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'User Tidak Ditemukan Silahkan coba lagi. REFRESH HALAMAN ANDA');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('User Tidak Ditemukan Silahkan coba lagi. REFRESH HALAMAN ANDA: ' . $th->getMessage());
                return back()->with('error', 'User Tidak Ditemukan Silahkan coba lagi. REFRESH HALAMAN ANDA.');
            }
        }
    }

    public function qrcodebimbingan()
    {
        $user = Auth::user();
        if (!$user->senso === 1) {
            return back()->with('error', 'Anda Tidak Memiliki Akses Ke Halaman Ini');
        }

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        if ($user->dmti_complete == 0) {
            return redirect()->route('profile.edit')->with('error', 'Silahkan lengkapi Data Anda Terlebih Dahulu');
        }

        if (empty($user->bimbingan_token)) {
            return back()->with('error', 'Anda Tidak Memiliki Token Silakan hubungi Petugas Untuk Generate Token');
        }


        try {
            $validatedQR = [
                'token' => $user->bimbingan_token,
            ];

            $json = json_encode($validatedQR);
            $QrCode = QrCode::size(300)->generate($json);

            $data['QrCode'] = $QrCode;
            $data['user'] = $user;

            // if ($user->bimbingan_token_expired_at < now()) {
            //     User::UpdateTokenBimbingan($user->id);
            // }

            return view('konseling.user.kodeqr-bimbingan', $data)->with('success', 'Token Anda Hanya Berlaku Selama 60 Menit.');
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Anda Tidak Memiliki Token Silakan hubungi Petugas Untuk Generate Token');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Token gagal ditampilkan: ' . $th->getMessage());
                return back()->with('error', 'Terjadi kesalahan, silakan coba lagi atau hubungi petugas.');
            }
        }
    }

    public function kameraBimbingan()
    {
        try {
            $user = Auth::user();

            $checkJadwalToday = JadwalBimbingan::where('tanggal', now()->format('Y-m-d'))->first();

            if (!$checkJadwalToday) {
                return redirect()->route('konseling.jadwal-bimbingan')->with('error', 'Jadwal Bimbingan Hari Ini Belum Tersedia');
            }

            if (!$user) {
                return back()->with('error', 'User tidak ditemukan ketika sqan QR');
            }

            $data['user'] = $user;

            return view('konseling.kamera-bimbingan', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'User tidak ditemukan ketika sqan QR');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('User tidak ditemukan ketika sqan QR: ' . $th->getMessage());
                return back()->with('error', 'User tidak ditemukan ketika sqan QR.');
            }
        }
    }

    public function storeKameraBimbingan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Token tidak valid');
        }

        $token = $request->token;

        try {
            $user = User::where('bimbingan_token', $token)->firstOrFail();

            if ($user->bimbingan_token_expired_at < now()) {
                User::updateTokenBimbingan($user->id);
                return back()->with('error', 'Token Anda Sudah Kadaluarsa Kami baru saja memperbarui Token Anda Silahkan coba lagi. REFRESH HALAMAN ANDA');
            }

            DB::beginTransaction();

            $jadwal = JadwalBimbingan::where('tanggal', now()->format('Y-m-d'))->firstOrFail();

            $presensiSenso = PresensiBimbingan::where('senso_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->first();

            if (!$presensiSenso) {
                return back()->with('error', 'Jadwal Presensi Hari Ini Tidak Ditemukan');
            }

            $jamPresensi = now()->format('H:i:s');
            $tanggalPresensi = now()->format('Y-m-d');

            if (now()->format('H:i:s') > $jadwal->jam_selesai) {
                $status = 'Terlambat';
            } else {
                $status = 'Hadir';
            }

            $presensiSenso->update([
                'jadwal_id' => $jadwal->id,
                'senso_id' => $user->id,
                'tanggal_presensi' => $tanggalPresensi,
                'jam_presensi' => $jamPresensi,
                'status' => $status,
            ]);

            DB::commit();

            return back()->with('success', 'Presensi Berhasil Disimpan, Link Feedback Mahasiswa Sudah Bisa Di Akses.');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'User atau Jadwal Tidak Ditemukan. Silahkan coba lagi. REFRESH HALAMAN ANDA');
        } catch (\Exception $th) {
            DB::rollBack();
            Log::error('Error menyimpan presensi: ' . $th->getMessage());
            return back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    public function kameraKonsultasi()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return back()->with('error', 'User tidak ditemukan ketika sqan QR');
            }

            $data['user'] = $user;

            return view('konseling.kamera-konsultasi', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'User tidak ditemukan ketika sqan QR');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('User tidak ditemukan ketika sqan QR: ' . $th->getMessage());
                return back()->with('error', 'User tidak ditemukan ketika sqan QR.');
            }
        }
    }

    public function storeKameraKonsultasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Token tidak valid');
        }

        $token = $request->token;

        try {
            $user = User::where('konsultasi_token', $token)->firstOrFail();
            // dd($user);

            if ($user->konsultasi_token_expired_at < now()) {
                User::updateTokenKonsultasi($user->id);
                return back()->with('error', 'Token Anda Sudah Kadaluarsa Kami baru saja memperbarui Token Anda Silahkan coba lagi. REFRESH HALAMAN ANDA');
            }

            return redirect()->route('konseling.form-konsultasi', $user->konsultasi_token);

            // return back()->with('success', 'QR Code valid silahkan lakukan konsultasi dengan petugas yang bersangkutan.');
        } catch (\Exception $th) {
            DB::rollBack();
            Log::error('Error Melakukan Konsultasi: ' . $th->getMessage());
            return back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    public function qrcodekonsultasi()
    {
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        if ($user->dmti_complete == 0) {
            return redirect()->route('profile.edit')->with('error', 'Silahkan lengkapi Data Anda Terlebih Dahulu');
        }

        if (empty($user->konsultasi_token)) {
            return back()->with('error', 'Anda Tidak Memiliki Token Silakan hubungi Petugas Untuk Generate Token');
        }


        try {
            $validatedQR = [
                'token' => $user->konsultasi_token,
            ];

            $json = json_encode($validatedQR);
            $QrCode = QrCode::size(300)->generate($json);

            $data['QrCode'] = $QrCode;
            $data['user'] = $user;

            // if ($user->konsultasi_token_expired_at < now()) {
            //     User::UpdateTokenKonsultasi($user->id);
            // }

            return view('konseling.user.kodeqr-konsultasi', $data)->with('success', 'Token Anda Hanya Berlaku Selama 60 Menit.');
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Anda Tidak Memiliki Token Silakan hubungi Petugas Untuk Generate Token');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Token gagal ditampilkan: ' . $th->getMessage());
                return back()->with('error', 'Terjadi kesalahan, silakan coba lagi atau hubungi petugas.');
            }
        }
    }
}
