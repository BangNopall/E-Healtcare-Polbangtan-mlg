<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Blok;
use App\Models\CDMI;
use App\Models\DMTI;
use App\Models\User;
use App\Models\Prodi;
use App\Exports\laporanRM;
use Illuminate\Support\Str;
use App\Models\DataPsikolog;
use Illuminate\Http\Request;
use App\Models\BimbinganSenso;
use App\Models\RequestRujukan;
use App\Models\JadwalBimbingan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Exports\laporanBimbingan;
use App\Models\FeedbackBimbingan;
use App\Models\PresensiBimbingan;
use App\Exports\laporanKonsultasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BimbinganKonselingController extends Controller
{
    public function dataSenso()
    {
        $data['sensos'] = User::where('senso', 1)->with('getCDMI')->orderBy('name', 'asc')->paginate(20);


        return view('konseling.data-senso', $data);
    }

    public function deleteSenso($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->senso = 0;
            $user->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data mahasiswa baru berhasil dihapus');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Mahasiswa tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Mahasiswa gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Mahasiswa gagal dihapus : ' . $th->getMessage());
                return back()->with('error', 'Data Mahasiswa gagal dihapus');
            }
        }
    }

    public function createSenso(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|exists:users,name',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput()->with('error', 'Data Mahasiswa Tidak Ditemukan');
            }

            DB::beginTransaction();
            $user = User::findOrFail($request->user_id);
            if ($user->senso == 1) {
                return back()->with('error', 'Data Sensuh sudah ada');
            }
            $user->senso = 1;
            $user->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data Sensuh baru berhasil ditambahkan');
        } catch (\Exception $th) {
            DB::rollBack();
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data sensuh tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Sensuh gagal ditambahkan');
            } else {
                Log::error('Data Sensuh gagal ditambahkan : ' . $th->getMessage());
                return back()->with('error', 'Data Sensuh gagal ditambahkan');
            }
        }
    }

    public function filterSenso(Request $request)
    {
        try {
            $input = $request->all();

            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter'] ?? '');

            $query = User::query();

            $query->where('role', 'Mahasiswa')->with('getCDMI');

            // Filter based on name or email if provided
            if (!empty($upperName)) {
                $query->where(function ($query) use ($upperName) {
                    $query->whereRaw('UPPER(name) LIKE ?', ['%' . $upperName . '%'])
                        ->orWhereRaw('UPPER(email) LIKE ?', ['%' . $upperName . '%']);
                });
            }

            $query->where('senso', 1);

            $data = $query->orderBy('name', 'asc')->paginate(20);


            $table = view('components.konseling.table-data-senso', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter User: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter User: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter User');
            }
        }
    }

    public function detailSenso($userId)
    {
        try {
            $user = User::find($userId);
            $data['user'] = $user;
            if ($user->dmti == true) {
                $dmti = DMTI::where('user_id', $user->id)->first();
                if ($dmti) {
                    $data['dmti'] = $dmti;
                }
            }
            if ($user->cdmi == true) {
                $cdmi = CDMI::where('user_id', $user->id)->first();
                if ($cdmi) {
                    $data['cdmi'] = $cdmi;
                }
            }
            $data['prodis'] = Prodi::all();
            $data['bloks'] = Blok::all();

            $data['anakSenso'] = BimbinganSenso::where('senso_id', $userId)
                ->with(['siswa.getCDMI'])
                ->get();

            return view('konseling.detail-data-senso', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Mahasiswa tidak ditemukan');
            } else {
                return back()->with('error', 'Gagal menampilkan detail Mahasiswa');
            }
        }
    }

    public function daftarsiswaAsuh(Request $request, $senso_id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $senso = User::find($senso_id);

        if (!$senso) {
            return back()->with('error', 'Data sensuh tidak ditemukan');
        }

        $siswa = User::find($request->user_id);

        if (!$siswa) {
            return back()->with('error', 'Data Mahasiswa tidak ditemukan');
        }

        $existing = BimbinganSenso::where('siswa_id', $siswa->id)->first();
        if ($existing) {
            return back()->with('error', 'Mahasiswa ini sudah terdaftar pada Senso lain');
        }

        // Proses Mendaftarkan siswa Asuh Senso

        try {
            DB::beginTransaction();

            $bimbinganSenso = BimbinganSenso::create([
                'senso_id' => $senso->id,
                'siswa_id' => $siswa->id,
            ]);

            DB::commit();

            // return back
            return redirect()->back()->with('success', 'Data Bimbingan Senso baru berhasil dibuat');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Bimbingan Sensuh tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Bimbingan Sensuh gagal disimpan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Bimbingan Sensuh gagal disimpan : ' . $th->getMessage());
                return back()->with('error', 'Data Bimbingan Sensuh gagal disimpan');
            }
        }
    }

    public function deleteSiswaAsuh($siswa_id)
    {
        try {
            DB::beginTransaction();
            $bimbinganSenso = BimbinganSenso::where('siswa_id', $siswa_id)->first();
            $bimbinganSenso->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Data Bimbingan Senso baru berhasil dihapus');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Bimbingan Sensuh tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Bimbingan Sensuh gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Bimbingan Sensuh gagal dihapus : ' . $th->getMessage());
                return back()->with('error', 'Data Bimbingan Sensuh gagal dihapus');
            }
        }
    }

    public function jadwalBimbingan()
    {
        $data['jadwal'] = JadwalBimbingan::orderBy('tanggal', 'desc')->paginate(20);
        $data['presensi'] = PresensiBimbingan::orderBy('tanggal_presensi', 'desc')->paginate(20);
        return view('konseling.jadwal', $data);
    }

    public function createJadwalBimbingan(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'materi' => 'required|string',
            ]);

            $checkJadwal = JadwalBimbingan::where('tanggal', $request->tanggal)->first();

            if ($checkJadwal) {
                return back()->with('error', 'Jadwal Bimbingan hari ini sudah dibuat');
            }

            DB::beginTransaction();

            $jadwal = JadwalBimbingan::create([
                'tanggal' => $request->tanggal,
                'materi' => $request->materi,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data Jadwal Bimbingan baru berhasil dibuat');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Jadwal Bimbingan tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Jadwal Bimbingan gagal disimpan');
            } else {
                Log::error('Data Jadwal Bimbingan gagal disimpan : ' . $th->getMessage());
                return back()->with('error', 'Data Jadwal Bimbingan gagal disimpan : ' . $th->getMessage());
            }
        }
    }

    public function destroyJadwalBimbingan($id)
    {
        try {
            DB::beginTransaction();
            $jadwal = JadwalBimbingan::findOrFail($id);
            $jadwal->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Data Jadwal Bimbingan baru berhasil dihapus');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Jadwal Bimbingan tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Jadwal Bimbingan gagal dihapus');
            } else {
                Log::error('Data Jadwal Bimbingan gagal dihapus : ' . $th->getMessage());
                return back()->with('error', 'Data Jadwal Bimbingan gagal dihapus');
            }
        }
    }

    public function filterJadwalBimbingan(Request $request)
    {
        try {
            $input = $request->all();

            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter'] ?? '');

            $query = JadwalBimbingan::query();

            // Filter based on name or email if provided
            if (!empty($upperName)) {
                $query->whereRaw('UPPER(materi) LIKE ?', ['%' . $upperName . '%']);
            }

            $data = $query->orderBy('tanggal', 'asc')->paginate(20);

            $table = view('components.konseling.table-jadwal', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Jadwal Bimbingan: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter Jadwal Bimbingan: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Jadwal Bimbingan');
            }
        }
    }

    public function riwayatFeedback()
    {
        // $data['feedback'] = FeedbackBimbingan::orderBy('jadwal_id', 'asc')->orderBy('senso_id', 'asc')->get();
        $data['feedback'] = FeedbackBimbingan::with(['siswa.getCDMI'])->orderBy('jadwal_id', 'desc')->orderBy('senso_id', 'asc')->paginate(20);

        // dd($data['feedback']);

        return view('konseling.riwayat-feedback', $data);
    }

    public function deleteFeedback($id)
    {
        try {
            DB::beginTransaction();
            $feedback = FeedbackBimbingan::findOrFail($id);
            $feedback->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Data Feedback Bimbingan baru berhasil dihapus');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Feedback Bimbingan tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Feedback Bimbingan gagal dihapus');
            } else {
                Log::error('Data Feedback Bimbingan gagal dihapus : ' . $th->getMessage());
                return back()->with('error', 'Data Feedback Bimbingan gagal dihapus');
            }
        }
    }

    public function showFeedback($id)
    {
        try {
            $feedback = FeedbackBimbingan::findOrFail($id);
            $data['feedback'] = $feedback;
            $user = $data['feedback']->siswa;
            $data['user'] = $user;
            if ($user->dmti == 1) {
                $dmti = DMTI::where('user_id', $user->id)->first();
                if ($dmti) {
                    $data['dmti'] = $dmti;
                }
            }
            if ($user->cdmi == 1) {
                $cdmi = CDMI::where('user_id', $user->id)->first();
                if ($cdmi) {
                    $data['cdmi'] = $cdmi;
                }
            }
            $data['prodis'] = Prodi::all();
            $data['bloks'] = Blok::all();

            return view('konseling.detail-feedback', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Feedback Bimbingan tidak ditemukan');
            } else {
                return back()->with('error', 'Gagal menampilkan detail Feedback Bimbingan');
            }
        }
    }

    public function filterFeedback(Request $request)
    {
        try {
            // Ambil semua input dari request
            $input = $request->all();

            // Validasi input
            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            // Ambil filter dari input dan trim whitespace
            $filter = trim($validatedData['filter'] ?? '');

            // Query dasar
            $query = FeedbackBimbingan::query();

            // Jika filter tidak kosong, tentukan tipe filter dan tambahkan kondisi ke query
            if (!empty($filter)) {
                if (preg_match('/^\d+$/', $filter)) {
                    // Jika filter adalah angka, anggap sebagai NIM
                    $query->whereHas('siswa', function ($query) use ($filter) {
                        $query->whereHas('getCDMI', function ($query) use ($filter) {
                            $query->where('nim', $filter);
                        });
                    });
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter)) {
                    // Jika filter cocok dengan format tanggal (YYYY-MM-DD), anggap sebagai tanggal
                    $query->whereHas('jadwal', function ($query) use ($filter) {
                        $query->whereDate('tanggal', $filter);
                    });
                } elseif ($this->isValidHumanDate($filter)) {
                    // Jika filter adalah tanggal manusiawi, konversikan ke format YYYY-MM-DD
                    $formattedDate = Carbon::parse($filter)->format('Y-m-d');
                    $query->whereHas('jadwal', function ($query) use ($formattedDate) {
                        $query->whereDate('tanggal', $formattedDate);
                    });
                } else {
                    // Jika filter bukan angka atau tanggal, anggap sebagai nama atau materi
                    $query->whereHas('siswa', function ($query) use ($filter) {
                        $query->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($filter) . '%']);
                    })->orWhereHas('jadwal', function ($query) use ($filter) {
                        $query->whereRaw('UPPER(materi) LIKE ?', ['%' . strtoupper($filter) . '%']);
                    });
                }
            }

            if (!$filter) {
                $data = FeedbackBimbingan::with(['siswa.getCDMI'])->orderBy('jadwal_id', 'desc')->orderBy('senso_id', 'asc')->paginate(20);
            } else {
                $data = $query->orderBy('jadwal_id', 'desc')->orderBy('senso_id', 'asc')->paginate(20);
            }

            // Render view tabel
            $table = view('components.konseling.table-riwayat-feedback', compact('data'))->render();

            // Return response JSON
            return response()->json(['table' => $table]);
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Feedback Bimbingan: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter Feedback Bimbingan: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Feedback Bimbingan');
            }
        }
    }

    // Fungsi untuk memvalidasi tanggal manusiawi
    private function isValidHumanDate($date)
    {
        try {
            Carbon::parse($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function formKonsultasi($token)
    {
        $user = User::where('konsultasi_token', $token)->first();

        if (!$user) {
            return back()->with('error', 'Data Mahasiswa tidak ditemukan');
        }

        $data['user'] = $user;

        if ($user->dmti == 1) {
            $dmti = DMTI::where('user_id', $user->id)->first();
            if ($dmti) {
                $data['dmti'] = $dmti;
            }
        }
        if ($user->cdmi == 1) {
            $cdmi = CDMI::where('user_id', $user->id)->first();
            if ($cdmi) {
                $data['cdmi'] = $cdmi;
            }
        }
        $data['prodis'] = Prodi::all();
        $data['bloks'] = Blok::all();

        // dd($data);

        return view('konseling.form.konsultasi', $data);
    }

    public function storeKonsultasi(Request $request, $userId)
    {

        $user = User::find($userId);
        // dd($request->all(), $user);

        if (!$user) {
            return back()->with('error', 'Data Mahasiswa tidak ditemukan');
        }

        try {
            $request->validate([
                'tanggal' => 'required|date',
                'keluhan' => 'nullable|string',
                'metode_psikologi' => 'nullable|string',
                'diagnosa' => 'nullable|string',
                'prognosis' => 'nullable|string',
                'intervensi' => 'nullable|string',
                'saran' => 'nullable|string',
                'rencana_tindak_lanjut' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $dataPsikolog = $user->dataPsikolog()->create([
                'tanggal' => $request->tanggal,
                'keluhan' => $request->keluhan,
                'metode_psikologi' => $request->metode_psikologi,
                'diagnosa' => $request->diagnosa,
                'prognosis' => $request->prognosis,
                'intervensi' => $request->intervensi,
                'saran' => $request->saran,
                'rencana_tindak_lanjut' => $request->rencana_tindak_lanjut,
            ]);

            DB::commit();

            return redirect()->route('konseling.review-konsultasi', $dataPsikolog->id)->with('success', 'Data Psikolog baru berhasil disimpan');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Psikolog tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Psikolog gagal disimpan');
            } else {
                Log::error('Data Psikolog gagal disimpan : ' . $th->getMessage());
                return back()->with('error', 'Data Psikolog gagal disimpan');
            }
        }
    }

    public function reviewKonsultasi($id)
    {
        try {
            $dataPsikolog = DataPsikolog::findOrFail($id);
            $data['dataPsikolog'] = $dataPsikolog;
            $user = $dataPsikolog->user;
            $data['user'] = $user;
            if ($user->dmti == 1) {
                $dmti = DMTI::where('user_id', $user->id)->first();
                if ($dmti) {
                    $data['dmti'] = $dmti;
                }
            }
            if ($user->cdmi == 1) {
                $cdmi = CDMI::where('user_id', $user->id)->first();
                if ($cdmi) {
                    $data['cdmi'] = $cdmi;
                }
            }
            $data['prodis'] = Prodi::all();
            $data['bloks'] = Blok::all();

            return view('konseling.form.review-konsultasi', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Psikolog tidak ditemukan');
            } else {
                return back()->with('error', 'Gagal menampilkan detail Psikolog');
            }
        }
    }

    public function riwayatKonsultasi()
    {
        $data['dataPsikolog'] = DataPsikolog::orderBy('tanggal', 'desc')->with(['user.getCDMI'])->paginate(20);

        return view('konseling.riwayat-konsultasi', $data);
    }

    public function deleteKonsultasi($id)
    {
        try {
            DB::beginTransaction();
            $dataPsikolog = DataPsikolog::findOrFail($id);
            // $dataPsikolog->delete();

            // check apakah sudah request surat atau belum
            $requestRujukan = RequestRujukan::where('data_id', $dataPsikolog->id)->first();
            if (!$requestRujukan) {
                $dataPsikolog->delete();
            } else {
                // jika status nya masih submitted, maka masih bisa dihapus
                if ($requestRujukan->status == 'submitted') {
                    $requestRujukan->delete();
                    $dataPsikolog->delete();
                } else {
                    return back()->with('error', 'Data Psikolog tidak bisa dihapus karena sudah request surat rujukan');
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Data Psikolog baru berhasil dihapus');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Psikolog tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Psikolog gagal dihapus');
            } else {
                Log::error('Data Psikolog gagal dihapus : ' . $th->getMessage());
                return back()->with('error', 'Data Psikolog gagal dihapus');
            }
        }
    }

    public function detailKonsultasi($id)
    {
        try {
            $dataPsikolog = DataPsikolog::findOrFail($id);
            $data['dataPsikolog'] = $dataPsikolog;
            $user = $dataPsikolog->user;
            $data['user'] = $user;
            if ($user->dmti == 1) {
                $dmti = DMTI::where('user_id', $user->id)->first();
                if ($dmti) {
                    $data['dmti'] = $dmti;
                }
            }
            if ($user->cdmi == 1) {
                $cdmi = CDMI::where('user_id', $user->id)->first();
                if ($cdmi) {
                    $data['cdmi'] = $cdmi;
                }
            }
            $data['prodis'] = Prodi::all();
            $data['bloks'] = Blok::all();

            return view('konseling.detail-konsultasi', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Psikolog tidak ditemukan');
            } else {
                return back()->with('error', 'Gagal menampilkan detail Psikolog');
            }
        }
    }


    public function requestSuratRujukan($id)
    {
        try {
            $dataPsikolog = DataPsikolog::findOrFail($id);
            $data['dataPsikolog'] = $dataPsikolog;
            $user = $dataPsikolog->user;
            $data['user'] = $user;
            if ($user->dmti == 1) {
                $dmti = DMTI::where('user_id', $user->id)->first();
                if ($dmti) {
                    $data['dmti'] = $dmti;
                }
            }
            if ($user->cdmi == 1) {
                $cdmi = CDMI::where('user_id', $user->id)->first();
                if ($cdmi) {
                    $data['cdmi'] = $cdmi;
                }
            }
            $data['prodis'] = Prodi::all();
            $data['bloks'] = Blok::all();


            // cari request sebelumnya dahulu 
            $requestRujukan = RequestRujukan::where('data_id', $dataPsikolog->id)->first();

            if ($requestRujukan) {
                return back()->with('error', 'Data Request Surat Rujukan sudah pernah dibuat');
            }

            DB::beginTransaction();

            $requestRujukan = RequestRujukan::create([
                'data_id' => $dataPsikolog->id,
                'status' => 'submitted',
            ]);

            DB::commit();

            return back()->with('success', 'Data Request Surat Rujukan baru berhasil dibuat');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Request Surat Rujukan tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Request Surat Rujukan gagal disimpan');
            } else {
                Log::error('Data Request Surat Rujukan gagal disimpan : ' . $th->getMessage());
                return back()->with('error', 'Data Request Surat Rujukan gagal disimpan');
            }
        }
    }

    public function filterKonsultasi(Request $request)
    {
        try {
            // Ambil semua input dari request
            $input = $request->all();

            // Validasi input
            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            // Ambil filter dari input dan trim whitespace
            $filter = trim($validatedData['filter'] ?? '');

            // Query dasar
            $query = DataPsikolog::query();

            // Jika filter tidak kosong, tentukan tipe filter dan tambahkan kondisi ke query
            if (!empty($filter)) {
                if (preg_match('/^\d+$/', $filter)) {
                    // Jika filter adalah angka, anggap sebagai NIM
                    $query->whereHas('user', function ($query) use ($filter) {
                        $query->whereHas('getCDMI', function ($query) use ($filter) {
                            $query->where('nim', $filter);
                        });
                    });
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter)) {
                    // Jika filter cocok dengan format tanggal (YYYY-MM-DD), anggap sebagai tanggal
                    $query->whereDate('tanggal', $filter);
                } elseif ($this->isValidHumanDate($filter)) {
                    // Jika filter adalah tanggal manusiawi, konversikan ke format YYYY-MM-DD
                    $formattedDate = Carbon::parse($filter)->format('Y-m-d');
                    $query->whereDate('tanggal', $formattedDate);
                } else {
                    // Jika filter bukan angka atau tanggal, anggap sebagai nama atau keluhan
                    $query->whereHas('user', function ($query) use ($filter) {
                        $query->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($filter) . '%']);
                    })->orWhereRaw('UPPER(keluhan) LIKE ?', ['%' . strtoupper($filter) . '%']);
                }
            }

            if (!$filter) {
                $data = DataPsikolog::orderBy('tanggal', 'desc')->with(['user.getCDMI'])->paginate(20);
            } else {
                $data = $query->orderBy('tanggal', 'desc')->with(['user.getCDMI'])->paginate(20);
            }

            // Render view tabel
            $table = view('components.konseling.table-riwayat-konsultasi', compact('data'))->render();

            // Return response JSON
            return response()->json(['table' => $table]);
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Konsultasi: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter Konsultasi: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Konsultasi');
            }
        }
    }

    public function hasilSuratRujukan()
    {
        try {
            $dataRequest = RequestRujukan::where('status', 'completed')->orderBy('created_at', 'desc')->with(['dataPsikolog.user.getCDMI', 'suratRujukan'])->paginate(20);

            $data['request'] = $dataRequest;

            return view('konseling.hasil-surat-rujukan', $data);
        } catch (\Throwable $th) {
            Log::error('Gagal menampilkan request rujukan: ' . $th->getMessage());
            return back()->with('error', 'Gagal menampilkan request rujukan');
        }
    }

    public function detailSuratRujukan($requestId)
    {
        try {
            $requestSurat = RequestRujukan::with(['dataPsikolog.user.getCDMI', 'suratRujukan'])->find($requestId);
            if (!$requestSurat) {
                return back()->with('error', 'Request Rujukan not found');
            }
            $data['requestSurat'] = $requestSurat;
            $data['dataPsikolog'] = $requestSurat->dataPsikolog;
            $surat = $requestSurat->suratRujukan;
            $data['surat'] = $surat;
            $data['user'] = User::find($surat->pasien_id);

            if ($data['user']->dmti == 1) {
                $dmti = DMTI::where('user_id', $data['user']->id)->first();
                if ($dmti) {
                    $data['dmti'] = $dmti;
                }
            }
            if ($data['user']->cdmi == 1) {
                $cdmi = CDMI::where('user_id', $data['user']->id)->first();
                if ($cdmi) {
                    $data['cdmi'] = $cdmi;
                }
            }
            $data['prodis'] = Prodi::all();
            $data['bloks'] = Blok::all();

            return view('kesehatan.form.laporan-keterangan-rujukan.hasil-surat', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Psikolog tidak ditemukan');
            } else {
                return back()->with('error', 'Gagal menampilkan detail Psikolog');
            }
        }
    }

    public function laporanKonseling()
    {
        return view('konseling.laporan-konseling');
    }

    public function printLaporanKonsultasi(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
        ]);

        $month = Carbon::parse($request->bulan)->month;
        $year = Carbon::parse($request->bulan)->year;

        // Array mapping untuk nama bulan dalam bahasa Indonesia
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $monthName = $months[$month];

        $konsul = DataPsikolog::whereMonth('created_at', $month)->whereYear('created_at', $year)
            ->with(['user.getCDMI'])
            ->get();

        // dd($konsul);
        try {
            if ($request->submit == 'pdf') {
                $pdf = Pdf::loadView('print.laporan-konsultasi', compact('konsul', 'monthName'))->setPaper('a3', 'landscape');
                $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', 'Laporan Konsultasi_' . $monthName) . '.pdf';
                $filePath = storage_path('app/public/' . $fileName);

                // Simpan file pdf
                $pdf->save($filePath);
                // Mengembalikan URL atau path ke file PDF yang baru disimpan
                return response()->download($filePath)->deleteFileAfterSend(true);
            }
            if ($request->submit == 'excel') {
                return Excel::download(new laporanKonsultasi($konsul, $monthName), 'Laporan Konsultasi_' . $monthName . '.xlsx');
            }
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Laporan Rekam Medis: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Laporan Rekam Medis:' . $th->getMessage());
        }
    }
    public function printLaporanFeedback(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
        ]);

        $month = Carbon::parse($request->bulan)->month;
        $year = Carbon::parse($request->bulan)->year;

        // Array mapping untuk nama bulan dalam bahasa Indonesia
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $monthName = $months[$month];

        $data = FeedbackBimbingan::whereMonth('created_at', $month)->whereYear('created_at', $year)
            ->with(['jadwal'])
            ->with(['senso'])
            ->with(['siswa.getCDMI'])
            ->get();

        // dd($konsul);
        try {
            if ($request->submit == 'pdf') {
                $pdf = Pdf::loadView('print.laporan-feedback', compact('data', 'monthName'))->setPaper('a3', 'landscape');
                $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', 'Laporan Bimbingan_' . $monthName) . '.pdf';
                $filePath = storage_path('app/public/' . $fileName);

                // Simpan file pdf
                $pdf->save($filePath);
                // Mengembalikan URL atau path ke file PDF yang baru disimpan
                return response()->download($filePath)->deleteFileAfterSend(true);
            }
            if ($request->submit == 'excel') {
                return Excel::download(new laporanBimbingan($data, $monthName), 'Laporan Bimbingan_' . $monthName . '.xlsx');
            }
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Laporan Konseling : ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Laporan Konseling :' . $th->getMessage());
        }
    }
}
