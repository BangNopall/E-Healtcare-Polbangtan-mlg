<?php

namespace App\Http\Controllers\User;

use App\Models\BimbinganSenso;
use App\Models\DataPsikolog;
use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class KonselingUserController extends Controller
{
    public function linkfeedback()
    {
        try {
            $auth = Auth::user();

            //cari Senso Dari user yang login
            $senso = BimbinganSenso::where('siswa_id', $auth->id)->first();

            // check jadwal bimbingan hari ini 
            $jadwal = JadwalBimbingan::where('tanggal', now()->format('Y-m-d'))->first();
            $data['jadwal'] = $jadwal;
            $data['senso'] = $senso;

            // jika hari ini ada jadwal bimbingan
            if ($jadwal) {
                // cek apakah senso nya sudah absen atau belum
                $presensi = $jadwal->presensi()->where('senso_id', $senso->senso_id)->first();

                // dd($presensi->status);

                if ($presensi->status === 'Hadir') {
                    // jika sudah absen
                    $data['linkFeedbackTerbaru'] = Route('user.konseling.form-feedback', [
                        'id' => $senso->senso_id,
                        'token' => $jadwal->token
                    ]);
                }
            }

            // jika tidak ada jadwal bimbingan hari ini cari feedback sebelumnya
            $data['feedback'] = FeedbackBimbingan::where('siswa_id', $auth->id)->with('jadwal', 'senso', 'siswa')->paginate(20);


            // dd($data);
            return view('konseling.user.link-feedback', $data);
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Bimbingan Sensuh tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Bimbingan Sensuh tidak ditemukan : ' . $th->getMessage());
                return back()->with('error', 'Data Bimbingan Sensuh tidak ditemukan');
            }
        }
    }

    public function formfeedback($id, $token)
    {
        $senso = User::findOrFail($id);

        $jadwal = JadwalBimbingan::where('token', $token)->firstOrFail();

        $data['senso'] = $senso;
        $data['jadwal'] = $jadwal;

        return view('konseling.user.form.feedback', $data);
    }

    public function storefeedback(Request $request)
    {
        try {

            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'jadwal_id' => 'required|exists:jadwal_bimbingans,id',
                'senso_id' => 'required|exists:users,id',
                'siswa_id' => 'required|exists:users,id',
                'feedback' => 'required|string',
            ]);

            if ($validator->fails()) {
                return back()->with('error', 'Data tidak valid');
            }

            $data = $validator->validated();

            DB::beginTransaction();

            $feedback = new FeedbackBimbingan();
            $feedback->senso_id = $data['senso_id'];
            $feedback->siswa_id = Auth::id();
            $feedback->jadwal_id = $data['jadwal_id'];
            $feedback->feedback = $data['feedback'];
            $feedback->save();

            DB::commit();
            return redirect()->route('user.konseling.link-feedback')->with('success', 'Feedback berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data tidak valid');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data tidak valid : ' . $th->getMessage());
                return back()->with('error', 'Data tidak valid');
            }
        }
    }

    public function reviewfeedback($id)
    {
        try {
            $feedback = FeedbackBimbingan::findOrFail($id);

            $data['feedback'] = $feedback;

            return view('konseling.user.form.review-feedback', $data);
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Feedback tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Feedback tidak ditemukan : ' . $th->getMessage());
                return back()->with('error', 'Data Feedback tidak ditemukan');
            }
        }
    }

    public function riwayatKonsultasi()
    {
        $auth = Auth::user();

        $data['dataPsikolog'] = DataPsikolog::where('user_id', $auth->id)->orderBy('tanggal', 'desc')->with(['user.getCDMI'])->paginate(20);

        return view('konseling.user.riwayat-konsultasi', $data);
    }

    public function detailKonsultasi($id)
    {
        try {
            $dataPsikolog = DataPsikolog::findOrFail($id);

            $data['dataPsikolog'] = $dataPsikolog;

            return view('konseling.user.detail-konsultasi', $data);
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Konsultasi tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Konsultasi tidak ditemukan : ' . $th->getMessage());
                return back()->with('error', 'Data Konsultasi tidak ditemukan');
            }
        }
    }

    private function isValidHumanDate($date)
    {
        try {
            Carbon::parse($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterKonsultasi(Request $request)
    {
        try {
            $auth = Auth::user();
            $input = $request->all();

            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            // Ambil filter dari input dan trim whitespace
            $filter = trim($validatedData['filter'] ?? '');

            // Query dasar
            $query = DataPsikolog::query();

            $query->where('user_id', $auth->id);

            // Jika filter tidak kosong, tentukan tipe filter dan tambahkan kondisi ke query

            if (!empty($filter)) {
                // cari berdasarkan metode_psikologi, keluhan,saran, dan tanggal
                if (preg_match('/^(\d{4}-\d{2}-\d{2})$/', $filter)) {
                    $query->whereDate('tanggal', $filter);
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter)) {
                    // Jika filter cocok dengan format tanggal (YYYY-MM-DD), anggap sebagai tanggal
                    $query->whereDate('tanggal', $filter);
                } elseif ($this->isValidHumanDate($filter)) {
                    // Jika filter adalah tanggal manusiawi, konversikan ke format YYYY-MM-DD
                    $formattedDate = Carbon::parse($filter)->format('Y-m-d');
                    $query->whereDate('tanggal', $formattedDate);
                } else {
                    $query->where(function ($query) use ($filter) {
                        $query->whereRaw('LOWER(metode_psikologi) LIKE ?', ['%' . strtolower($filter) . '%'])
                            ->orWhereRaw('LOWER(keluhan) LIKE ?', ['%' . strtolower($filter) . '%'])
                            ->orWhereRaw('LOWER(saran) LIKE ?', ['%' . strtolower($filter) . '%']);
                    });
                }
            }

            if (!$filter) {
                $data = DataPsikolog::where('user_id', $auth->id)->orderBy('tanggal', 'desc')->with(['user.getCDMI'])->paginate(20);
            } else {
                $data = $query->orderBy('tanggal', 'desc')->with(['user.getCDMI'])->paginate(20);
            }

            $table = view('components.konseling.table-riwayat-konsultasi-user', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Throwable $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Konsultasi: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter Konsultasi: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Konsultasi');
            }
        }
    }
}
