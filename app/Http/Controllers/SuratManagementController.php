<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use App\Models\CDMI;
use App\Models\DMTI;
use App\Models\User;
use App\Models\Prodi;
use App\Models\ObatLog;
use App\Models\HistoriRs;
use App\Models\RekamMedis;
use Illuminate\Support\Str;
use App\Models\DataPsikolog;
use App\Models\SuratRujukan;
use Illuminate\Http\Request;
use App\Models\InventoryObat;
use App\Models\RequestRujukan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\SuratKeteranganSakit;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Models\SuratKeteranganBerobat;
use App\Models\SuratKeteranganSehat;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SuratManagementController extends Controller
{
    public function getFormType($user_id)
    {
        $user = User::find($user_id);

        if ($user->role == 'Mahasiswa' || $user->role == 'Karyawan') {
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
            return view('kesehatan.form.tipe-laporan', $data);
        } else {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak memiliki akses ke halaman ini');
        }
    }

    public function requestSurat(Request $request, $user_id)
    {
        try {

            // dd($request->all());
            $user = User::find($user_id);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            if ($user->role == 'Karyawan' && $user->dmti_complete == 0) {
                return back()->with('error', 'Data Karyawan tidak lengkap');
            } elseif ($user->role == 'Mahasiswa' && $user->cdmi_complete == 0) {
                return back()->with('error', 'Data Mahasiswa tidak lengkap');
            } elseif ($user->role == 'Karyawan' && $user->dmti_complete == 0) {
                return back()->with('error', 'Data Mahasiswa tidak lengkap');
            }

            $request->validate([
                'tipe' => ['required', 'string', Rule::in(['surat_rujukan', 'surat_keterangan_obat', 'surat_keterangan_sakit'])],
            ]);

            $tipe = $request->tipe;

            if ($tipe == 'surat_rujukan') {
                return redirect()->route('kesehatan.form.surat-rujukan', $user_id);
            } elseif ($tipe == 'surat_keterangan_obat') {
                return redirect()->route('kesehatan.form.surat-keterangan-obat', $user_id);
            } elseif ($tipe == 'surat_keterangan_sakit') {
                return redirect()->route('kesehatan.form.surat-keterangan-sakit', $user_id);
            } else {
                return back()->with('error', 'Tipe surat tidak ditemukan');
            }
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Mahasiswa: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter Mahasiswa: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Mahasiswa');
            }
        }
    }

    public function StoreSuratKeteranganObat(Request $request, $rm_id)
    {
        try {
            // Retrieve user and dokter information
            $rm = RekamMedis::find($rm_id);
            $user = User::find($rm->pasien_id);
            $dokter_id = auth()->user()->id;
            $pasien_id = $user->id;

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            // Validate the request data
            $request->validate([
                'nama_dokter' => ['required', 'string'],
                'jabatan_dokter' => ['required', 'string', 'in:Dokter,Psikolog,Perawat'],
                'nama_pasien' => ['required', 'string'],
                'keluhan' => ['nullable', 'string'],
                'pemeriksaan' => ['nullable', 'string'],
                'diagnosa' => ['required', 'string'],
                'nik' => ['required', 'string'],
                'ttl' => ['required', 'string'],
                'jenis_kelamin' => ['required', 'string', 'in:pria,wanita'],
                'usia' => ['required', 'integer'],
                'no_hp' => ['required', 'string'],
                'obat_id' => ['array'],
                'kuantitas' => ['array'],
            ]);

            // Begin transaction
            DB::beginTransaction();

            // Generate the surat keterangan berobat number
            // $nomor_surat = 'SKB-' . date('Ymd') . '-' . str_pad(SuratKeteranganBerobat::max('id') + 1, 3, '0', STR_PAD_LEFT);

            $data['tindakan'] = json_decode($rm->tindakan, true);
            // Prepare data for SuratKeteranganBerobat
            $data = [
                'dokter_id' => $dokter_id,
                'pasien_id' => $pasien_id,
                'nama_dokter' => $request->nama_dokter,
                'jabatan_dokter' => $request->jabatan_dokter,
                'jabatan_pasien' => $user->role == 'Mahasiswa' ? 'Mahasiswa' : 'Karyawan',
                'nama_pasien' => $request->nama_pasien,
                'keluhan' => NULL,
                'pemeriksaan' => NULL,
                'diagnosa' => $request->diagnosa,

                'nik' => $request->nik,
                'no_bpjs' => $user->getDMTI->no_bpjs,
                'ttl' => $request->ttl,
                'jenis_kelamin' => $request->jenis_kelamin,
                'usia' => $request->usia,
                'no_hp' => $request->no_hp,
                'golongan_darah' => $user->getDMTI->golongan_darah,

                'obat_id' => $data['tindakan']['obat_id'],
                'kuantitas' => $data['tindakan']['jumlah_obat'],
                // 'nomor_surat' => $nomor_surat,
            ];

            // Add additional data if user role is Mahasiswa
            if ($user->role == 'Mahasiswa') {
                $data = array_merge($data, [
                    'nim' => $user->getCDMI->nim,
                    'no_ruangan' => $user->getCDMI->no_ruangan,
                    'prodi_id' => $user->getCDMI->prodi_id,
                    'blok_id' => $user->getCDMI->blok_id,
                ]);
            }

            // Create SuratKeteranganBerobat record
            $suratKeteranganBerobat = SuratKeteranganBerobat::create($data);

            $suratKeteranganBerobat->withObat = 1;
            $suratKeteranganBerobat->save();
            foreach ($data['obat_id'] as $key => $obat_id) {
                // Validate if the obat_id exists in inventory_obats
                $obat = InventoryObat::find($obat_id);
                if (!$obat) {
                    throw new \Exception("Obat with ID {$obat_id} not found in inventory_obats.");
                }

                // Create ObatLog record
                ObatLog::create([
                    'obat_id' => $obat_id,
                    'Qty' => $data['kuantitas'][$key],
                    'production_date' => now()->toDateString(),
                    'expired_date' => now()->addYear()->toDateString(),
                    'user_id' => $dokter_id,
                    'skb_id' => $suratKeteranganBerobat->id,
                ]);
            }

            // Commit transaction
            DB::commit();

            return redirect()->route('kesehatan.lkb.hasil-surat', $suratKeteranganBerobat->id)->with('success', 'Berhasil Membuat Surat Keterangan Berobat');
        } catch (\Exception $th) {
            DB::rollBack();
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal Membuat Surat Keterangan Berobat: ' . $th->getMessage());
            } else {
                Log::error('Gagal Membuat Surat Keterangan Berobat: ' . $th->getMessage());
                return back()->with('error', 'Gagal Membuat Surat Keterangan Berobat : ' . $th->getMessage());
            }
        }
    }

    public function suratKeteranganObat($rm_id)
    {
        $rm = RekamMedis::find($rm_id);
        if (!$rm) {
            return back()->with('error', 'Rekam Medis tidak ditemukan!');
        }
        $user = User::find($rm->pasien_id);

        if ($user->role == 'Mahasiswa' || $user->role == 'Karyawan') {
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
            $data['rm'] = $rm;
            $data['tindakan'] = json_decode($rm->tindakan, true);

            return view('kesehatan.form.laporan-keterangan-berobat.buat-surat', $data);
        } else {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak memiliki akses ke halaman ini');
        }
    }

    public function reviewSuratKeteranganObat($id)
    {
        $surat = SuratKeteranganBerobat::find($id);
        if (!$surat) {
            return back()->with('error', 'Surat tidak ditemukan');
        }
        $data['surat'] = $surat;
        $user = User::find($surat->pasien_id);
        if ($user->role == 'Mahasiswa' || $user->role == 'Karyawan') {
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
            if ($surat->withObat == 1) {
                $data['obatLogs'] = $surat->obatLogs;
            }
            return view('kesehatan.form.laporan-keterangan-berobat.hasil-surat', $data);
        } else {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak memiliki akses ke halaman ini');
        }
    }

    public function printSuratKeteranganObat($id)
    {
        $surat = SuratKeteranganBerobat::find($id);
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        if ($surat->withObat == 1) {
            $data['obatLogs'] = $surat->obatLogs;
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('print.skb', $data)->setPaper('a4', 'portrait');

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat->nomor_surat) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return redirect()->route('kesehatan.kamera')->with('success', 'Berhasil Mencetak Surat Keterangan Berobat');

            // setelah download kembali ke view kamera
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Surat Keterangan Berobat: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Surat Keterangan Berobat' . $th->getMessage());
        }
    }

    public function riwayatLaporan()
    {
        // Pagination control
        $pagination = 10;

        // skb
        $SuratKeteranganBerobat = SuratKeteranganBerobat::orderBy('id', 'desc')->with('pasien')->paginate($pagination);
        $data['skbs'] = $SuratKeteranganBerobat;

        // skse
        $SuratKeteranganSehat = SuratKeteranganSehat::orderBy('id', 'desc')->with('pasien')->paginate($pagination);
        $data['skses'] = $SuratKeteranganSehat;

        // sks
        $skss = SuratKeteranganSakit::orderBy('id', 'desc')->paginate($pagination);
        $data['skss'] = $skss;

        // sr
        $srs = SuratRujukan::orderBy('id', 'desc')->paginate($pagination);
        $data['srss'] = $srs;

        $dataTotal = [
            'totalSKBS' => SuratKeteranganBerobat::count(),
            'totalSKSE' => SuratKeteranganSehat::count(),
            'totalSKS' => SuratKeteranganSakit::count(),
            'totalSRS' => SuratRujukan::count(),
        ];

        arsort($dataTotal);
        $keyPagination = key($dataTotal);
        if ($keyPagination == 'totalSKBS') {
            $keyPagination = 'skbs';
        } elseif ($keyPagination == 'totalSKSE') {
            $keyPagination = 'skses';
        } elseif ($keyPagination == 'totalSKS') {
            $keyPagination = 'skss';
        } elseif ($keyPagination == 'totalSRS') {
            $keyPagination = 'srss';
        }

        // dd($dataTotal, $keyPagination);

        // $paginationControl 

        return view('kesehatan.riwayat', $data, compact('keyPagination'));
    }

    public function showSuratKeteranganObat($nomor_surat)
    {
        $surat = SuratKeteranganBerobat::where('nomor_surat', $nomor_surat)->first();
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        if ($surat->withObat == 1) {
            $data['obatLogs'] = $surat->obatLogs;
        }

        return view('kesehatan.detail-skb', $data);
    }

    public function deleteSuratKeteranganObat($id)
    {
        try {
            $surat = SuratKeteranganBerobat::find($id);
            if (!$surat) {
                return back()->with('error', 'Surat Keterangan Berobat not found');
            }

            $surat->delete();

            return redirect()->route('kesehatan.riwayat')->with('success', 'Berhasil Menghapus Surat Keterangan Berobat');
        } catch (\Exception $th) {
            Log::error('Gagal Menghapus Surat Keterangan Berobat: ' . $th->getMessage());
            return back()->with('error', 'Gagal Menghapus Surat Keterangan Berobat');
        }
    }

    public function suratKeteranganSakit($rm_id)
    {
        $rm = RekamMedis::find($rm_id);
        if (!$rm) {
            return back()->with('error', 'Rekam Medis tidak ditemukan!');
        }
        $user = User::find($rm->pasien_id);
        if ($user->role == 'Mahasiswa' || $user->role == 'Karyawan') {
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
            $data['rm'] = $rm;
            $data['tindakan'] = json_decode($rm->tindakan, true);
            return view('kesehatan.form.laporan-keterangan-sakit.buat-surat', $data);
        } else {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak memiliki akses ke halaman ini');
        }
    }

    public function StoreSuratKeteranganSakit(Request $request, $rm_id)
    {
        try {
            $rm = RekamMedis::find($rm_id);
            $user = User::find($rm->pasien_id);
            $dokter = auth()->user();
            $pasien = $user->id;

            if (!$user) {
                return back()->with('error', 'User not found');
            }


            // Validate the request data
            $request->validate([
                'nama_pasien' => ['required', 'string'],
                'nik' => ['required', 'string'],
                'ttl' => ['required', 'string'],
                'jenis_kelamin' => ['required', 'string', 'in:pria,wanita'],
                'usia' => ['required', 'integer'],
                'no_hp' => ['required', 'string'],
                'tanggal_mulai' => ['required', 'date'],
                'tanggal_akhir' => ['required', 'date'],
                'lama_sakit' => ['required', 'integer'],
            ]);


            if ($dokter->role == 'Dokter') {
                $jabatan_dokter = 'Dokter';
            } elseif ($dokter->role == 'Psikolog') {
                $jabatan_dokter = 'Psikolog';
            } elseif ($dokter->role == 'Perawat') {
                $jabatan_dokter = 'Perawat';
            } else {
                $jabatan_dokter = 'Dokter';
            }

            // Begin transaction
            DB::beginTransaction();

            // Prepare data for SuratKeteranganSakit
            $data = [
                'dokter_id' => $dokter->id, // Use dokter ID
                'pasien_id' => $pasien,
                'nama_dokter' => $dokter->name,
                'jabatan_dokter' => $jabatan_dokter,
                'jabatan_pasien' => $user->role == 'Mahasiswa' ? 'Mahasiswa' : 'Karyawan',
                'nama_pasien' => $request->nama_pasien,
                'nik' => $request->nik,
                'ttl' => $request->ttl,
                'jenis_kelamin' => $request->jenis_kelamin,
                'usia' => $request->usia,
                'no_hp' => $request->no_hp,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'lama_sakit' => $request->lama_sakit,
            ];

            // Add additional data if user role is Mahasiswa
            if ($user->role == 'Mahasiswa') {
                $data = array_merge($data, [
                    'nim' => $user->getCDMI->nim,
                    'no_ruangan' => $user->getCDMI->no_ruangan,
                    'prodi_id' => $user->getCDMI->prodi_id,
                    'blok_id' => $user->getCDMI->blok_id,
                ]);
            }

            // Create SuratKeteranganSakit record
            $SuratKeteranganSakit = SuratKeteranganSakit::create($data);

            // Commit transaction
            DB::commit();

            return redirect()->route('kesehatan.lks.hasil-surat', $SuratKeteranganSakit->id)->with('success', 'Berhasil Membuat Surat Keterangan Sakit');
        } catch (\Exception $th) {
            DB::rollBack();
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal Membuat Surat Keterangan Sakit: ' . $th->getMessage());
            } else {
                Log::error('Gagal Membuat Surat Keterangan Sakit: ' . $th->getMessage());
                return back()->with('error', 'Gagal Membuat Surat Keterangan Sakit : ' . $th->getMessage());
            }
        }
    }

    public function reviewSuratKeteranganSakit($id)
    {
        $surat = SuratKeteranganSakit::find($id);
        if (!$surat) {
            return back()->with('error', 'Surat tidak ditemukan');
        }
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

        return view('kesehatan.form.laporan-keterangan-sakit.hasil-surat', $data);
    }

    public function printSuratKeteranganSakit($id)
    {
        $surat = SuratKeteranganSakit::find($id);
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        if ($surat->withObat == 1) {
            $data['obatLogs'] = $surat->obatLogs;
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('print.sks', $data)->setPaper('a4', 'portrait');

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat->nomor_surat) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return redirect()->route('kesehatan.kamera')->with('success', 'Berhasil Mencetak Surat Keterangan Berobat');

            // setelah download kembali ke view kamera
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Surat Keterangan Sakit: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Surat Keterangan Sakit' . $th->getMessage());
        }
    }

    public function showSuratKeteranganSakit($nomor_surat)
    {
        $surat = SuratKeteranganSakit::where('nomor_surat', $nomor_surat)->first();
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        if ($surat->withObat == 1) {
            $data['obatLogs'] = $surat->obatLogs;
        }

        return view('kesehatan.detail-sks', $data);
    }

    public function deleteSuratKeteranganSakit($id)
    {
        try {
            $surat = SuratKeteranganSakit::find($id);
            if (!$surat) {
                return back()->with('error', 'Surat Keterangan Sakit not found');
            }

            $surat->delete();

            return redirect()->route('kesehatan.riwayat')->with('success', 'Berhasil Menghapus Surat Keterangan Sakit');
        } catch (\Exception $th) {
            Log::error('Gagal Menghapus Surat Keterangan Sakit: ' . $th->getMessage());
            return back()->with('error', 'Gagal Menghapus Surat Keterangan Sakit');
        }
    }

    public function suratRujukan($rm_id)
    {
        $rm = RekamMedis::find($rm_id);
        if (!$rm) {
            return back()->with('error', 'Rekam Medis tidak ditemukan!');
        }
        $user = User::find($rm->pasien_id);

        if ($user->role == 'Mahasiswa' || $user->role == 'Karyawan') {
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
            $data['rm'] = $rm;
            $data['tindakan'] = json_decode($rm->tindakan, true);
            return view('kesehatan.form.laporan-keterangan-rujukan.buat-surat', $data);
        } else {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak memiliki akses ke halaman ini');
        }
    }

    public function StoreSuratRujukan(Request $request, $rm_id)
    {
        try {
            // Retrieve user and dokter information
            $rm = RekamMedis::find($rm_id);
            $user = User::find($rm->pasien_id);
            $dokter = auth()->user();
            $pasien_id = $user->id;

            // Validate the request data
            $request->validate([
                'nama_dokter' => ['required', 'string'],
                'nama_pasien' => ['required', 'string'],
                'keluhan' => ['required', 'string'],
                'diagnosa' => ['required', 'string'],
                'kasus' => ['required', 'string'],
                'tindakan' => ['required', 'string'],

                'nik' => ['required', 'string'],
                'ttl' => ['required', 'string'],
                'jenis_kelamin' => ['required', 'string', 'in:pria,wanita'],
                'usia' => ['required', 'integer'],
                'no_hp' => ['required', 'string'],
                'nama_rs' => ['required', 'string'],
                'rs_id' => ['nullable', 'string'],
            ]);

            if ($request->rs_id) {
                $nama_rs_old = $request->nama_rs;
                $rs_id_old = $request->rs_id;

                $rs = HistoriRs::find($rs_id_old);

                if ($rs->nama_rs == $nama_rs_old) {
                    $nama_rs = $rs->nama_rs;
                } else {
                    $nama_rs = HistoriRs::create([
                        'nama_rs' => $request->nama_rs,
                    ]);
                    $nama_rs = $nama_rs->nama_rs;
                }
            } else {
                // $upperNameRS = Str::upper($request->nama_rs);
                $upperNameRS = $request->nama_rs;
                $query = HistoriRs::query();

                if (!empty($upperNameRS)) {
                    $query->where(function ($query) use ($upperNameRS) {
                        $query->whereRaw('nama_rs LIKE ?', ['%' . $upperNameRS . '%']);
                        // $query->whereRaw('UPPER(nama_rs) LIKE ?', ['%' . $upperNameRS . '%']);
                    });
                }

                $rs = $query->first();

                if ($rs) {
                    $nama_rs = $rs->nama_rs;
                } else {
                    $nama_rs = HistoriRs::create([
                        'nama_rs' => $request->nama_rs,
                    ]);
                    $nama_rs = $nama_rs->nama_rs;
                }
            }

            // dd($nama_rs);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            // Begin transaction
            DB::beginTransaction();

            // Generate the surat keterangan berobat number
            // $nomor_surat = 'SKB-' . date('Ymd') . '-' . str_pad(SuratKeteranganBerobat::max('id') + 1, 3, '0', STR_PAD_LEFT);

            if ($dokter->role == 'Dokter') {
                $jabatan_dokter = 'Dokter';
            } elseif ($dokter->role == 'Psikolog') {
                $jabatan_dokter = 'Psikolog';
            } elseif ($dokter->role == 'Perawat') {
                $jabatan_dokter = 'Perawat';
            } else {
                $jabatan_dokter = 'Dokter';
            }

            // Prepare data for SuratKeteranganBerobat
            $data = [
                'dokter_id' => $dokter->id,
                'pasien_id' => $pasien_id,
                'nama_dokter' => $request->nama_dokter,
                'jabatan_dokter' => $jabatan_dokter,
                'jabatan_pasien' => $user->role == 'Mahasiswa' ? 'Mahasiswa' : 'Karyawan',
                'nama_pasien' => $request->nama_pasien,

                'keluhan' => $request->keluhan,
                'diagnosa' => $request->diagnosa,
                'kasus' => $request->kasus,
                'tindakan' => $request->tindakan,

                'nik' => $request->nik,
                'ttl' => $request->ttl,
                'jenis_kelamin' => $request->jenis_kelamin,
                'usia' => $request->usia,
                'no_hp' => $request->no_hp,
                'nama_rs' => $nama_rs,
                // 'nomor_surat' => $nomor_surat,
            ];

            // Add additional data if user role is Mahasiswa
            if ($user->role == 'Mahasiswa') {
                $data = array_merge($data, [
                    'nim' => $user->getCDMI->nim,
                    'no_ruangan' => $user->getCDMI->no_ruangan,
                    'prodi_id' => $user->getCDMI->prodi_id,
                    'blok_id' => $user->getCDMI->blok_id,
                ]);
            }

            // Create SuratKeteranganBerobat record
            $suratRujukan = SuratRujukan::create($data);

            // Check if obat_id and kuantitas are present in the request
            $suratRujukan->save();

            // Commit transaction
            DB::commit();

            return redirect()->route('kesehatan.lkr.hasil-surat', $suratRujukan->id)->with('success', 'Berhasil Membuat Surat Rujukan');
        } catch (\Exception $th) {
            DB::rollBack();
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal Membuat Surat Rujukan : ' . $th->getMessage());
            } else {
                Log::error('Gagal Membuat Surat Rujukan : ' . $th->getMessage());
                return back()->with('error', 'Gagal Membuat Surat Rujukan : ' . $th->getMessage());
            }
        }
    }

    public function reviewSuratRujukan($id)
    {
        $surat = SuratRujukan::find($id);
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

        return view('kesehatan.form.laporan-keterangan-rujukan.hasil-surat', $data);
    }

    public function printSuratRujukan($id)
    {
        $surat = SuratRujukan::find($id);
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        try {
            // Generate PDF
            $pdf = Pdf::loadView('print.sr', $data)->setPaper('a4', 'portrait');

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat->nomor_surat) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards dan kembali ke halaman sebelumnya
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return redirect()->route('kesehatan.kamera')->with('success', 'Berhasil Mencetak Surat Keterangan Berobat');

            // setelah download kembali ke view kamera
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Surat Rujukan: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Surat Rujukan' . $th->getMessage());
        }
    }

    public function showSuratRujukan($nomor_surat)
    {
        $surat = SuratRujukan::where('nomor_surat', $nomor_surat)->first();
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        return view('kesehatan.detail-sr', $data);
    }

    public function deleteSuratRujukan($id)
    {
        try {
            $surat = SuratRujukan::find($id);
            if (!$surat) {
                return back()->with('error', 'Surat Keterangan Sakit not found');
            }

            $surat->delete();

            return redirect()->route('kesehatan.riwayat')->with('success', 'Berhasil Menghapus Surat Rujukan');
        } catch (\Exception $th) {
            Log::error('Gagal Menghapus Surat Rujukan: ' . $th->getMessage());
            return back()->with('error', 'Gagal Menghapus Surat Rujukan');
        }
    }

    public function suratKeteranganSehat($rm_id)
    {
        $rm = RekamMedis::find($rm_id);
        if (!$rm) {
            return back()->with('error', 'Rekam Medis tidak ditemukan!');
        }
        $user = User::find($rm->pasien_id);

        if ($user->role == 'Mahasiswa' || $user->role == 'Karyawan') {
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
            $data['rm'] = $rm;
            $data['tindakan'] = json_decode($rm->tindakan, true);
            return view('kesehatan.form.laporan-keterangan-sehat.buat-surat', $data);
        } else {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak memiliki akses ke halaman ini');
        }
    }

    public function StoreSuratKeteranganSehat(Request $request, $rm_id)
    {
        try {
            $rm = RekamMedis::find($rm_id);
            $user = User::find($rm->pasien_id);
            $dokter = auth()->user();
            $pasien = $user->id;

            if (!$user) {
                return back()->with('error', 'User not found');
            }


            // Validate the request data
            $request->validate([
                'nama_pasien' => ['required', 'string'],
                'nik' => ['required', 'string'],
                'ttl' => ['required', 'string'],
                'jenis_kelamin' => ['required', 'string', 'in:pria,wanita'],
                'usia' => ['required', 'integer'],
                'no_hp' => ['required', 'string'],
                'tinggi_badan' => ['required', 'string'],
                'berat_badan' => ['required', 'string'],
                'syarat' => ['required', 'string'],
                'jabatan_dokter' => ['required', 'string'],
            ]);

            // Begin transaction
            DB::beginTransaction();

            // Prepare data for SuratKeteranganSakit
            $data = [
                'dokter_id' => $dokter->id, // Use dokter ID
                'pasien_id' => $pasien,
                'nama_dokter' => $dokter->name,
                'jabatan_dokter' => $request->jabatan_dokter,
                'jabatan_pasien' => $user->role == 'Mahasiswa' ? 'Mahasiswa' : 'Karyawan',
                'nama_pasien' => $request->nama_pasien,
                'nik' => $request->nik,
                'ttl' => $request->ttl,
                'jenis_kelamin' => $request->jenis_kelamin,
                'usia' => $request->usia,
                'no_hp' => $request->no_hp,
                'tinggi_badan' => $request->tinggi_badan,
                'berat_badan' => $request->berat_badan,
                'syarat' => $request->syarat,
            ];

            // Add additional data if user role is Mahasiswa
            if ($user->role == 'Mahasiswa') {
                $data = array_merge($data, [
                    'nim' => $user->getCDMI->nim,
                    'no_ruangan' => $user->getCDMI->no_ruangan,
                    'prodi_id' => $user->getCDMI->prodi_id,
                    'blok_id' => $user->getCDMI->blok_id,
                ]);
            }

            // Create SuratKeteranganSakit record
            $SuratKeteranganSehat = SuratKeteranganSehat::create($data);

            // Commit transaction
            DB::commit();

            return redirect()->route('kesehatan.lkse.hasil-surat', $SuratKeteranganSehat->id)->with('success', 'Berhasil Membuat Surat Keterangan Sakit');
        } catch (\Exception $th) {
            DB::rollBack();
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal Membuat Surat Keterangan Sakit: ' . $th->getMessage());
            } else {
                Log::error('Gagal Membuat Surat Keterangan Sakit: ' . $th->getMessage());
                return back()->with('error', 'Gagal Membuat Surat Keterangan Sakit : ' . $th->getMessage());
            }
        }
    }

    public function reviewSuratKeteranganSehat($id)
    {
        $surat = SuratKeteranganSehat::find($id);
        if (!$surat) {
            return back()->with('error', 'Surat tidak ditemukan');
        }
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

        return view('kesehatan.form.laporan-keterangan-sehat.hasil-surat', $data);
    }

    public function printSuratKeteranganSehat($id)
    {
        $surat = SuratKeteranganSehat::find($id);
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        if ($surat->withObat == 1) {
            $data['obatLogs'] = $surat->obatLogs;
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('print.skse', $data)->setPaper('a4', 'portrait');

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat->nomor_surat) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return redirect()->route('kesehatan.kamera')->with('success', 'Berhasil Mencetak Surat Keterangan Berobat');

            // setelah download kembali ke view kamera
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Surat Keterangan Sehat: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Surat Keterangan Sehat' . $th->getMessage());
        }
    }

    public function showSuratKeteranganSehat($nomor_surat)
    {
        $surat = SuratKeteranganSehat::where('nomor_surat', $nomor_surat)->first();
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        return view('kesehatan.detail-skse', $data);
    }

    public function deleteSuratKeteranganSehat($id)
    {
        try {
            $surat = SuratKeteranganSehat::find($id);
            if (!$surat) {
                return back()->with('error', 'Surat Keterangan Sehat not found');
            }

            $surat->delete();

            return redirect()->route('kesehatan.riwayat')->with('success', 'Berhasil Menghapus Surat Keterangan Sehat');
        } catch (\Exception $th) {
            Log::error('Gagal Menghapus Surat Keterangan Sehat: ' . $th->getMessage());
            return back()->with('error', 'Gagal Menghapus Surat Keterangan Sehat');
        }
    }

    public function requestSuratRujukanKonseling()
    {
        try {
            $dataRequest = RequestRujukan::orderBy('created_at', 'desc')->with(['dataPsikolog.user.getCDMI'])->paginate(20);

            $data['request'] = $dataRequest;

            return view('kesehatan.request-konseling', $data);
        } catch (\Throwable $th) {
            Log::error('Gagal menampilkan request rujukan: ' . $th->getMessage());
            return back()->with('error', 'Gagal menampilkan request rujukan');
        }
    }

    public function detailKonsultasiFromMedical($id, $requestId)
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
            $data['requestId'] = $requestId;

            return view('kesehatan.form.request-surat-rujukan', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Psikolog tidak ditemukan');
            } else {
                return back()->with('error', 'Gagal menampilkan detail Psikolog');
            }
        }
    }

    public function requestSuratRujukanKonselingStore(Request $request, $user_id, $requestId)
    {
        // dd($request->all(), $user_id, $requestId);
        try {
            // Validate the request data
            $request->validate([
                'nama_dokter' => ['required', 'string'],
                'nama_pasien' => ['required', 'string'],
                'keluhan' => ['required', 'string'],
                'diagnosa' => ['required', 'string'],
                'kasus' => ['required', 'string'],
                'tindakan' => ['required', 'string'],

                'nik' => ['required', 'string'],
                'ttl' => ['required', 'string'],
                'jenis_kelamin' => ['required', 'string', 'in:pria,wanita'],
                'usia' => ['required', 'integer'],
                'no_hp' => ['required', 'string'],
                'nama_rs' => ['required', 'string'],
                'rs_id' => ['nullable', 'string'],
            ]);

            // dd($request->all(), $user_id);


            // Retrieve user and dokter information
            $user = User::find($user_id);
            $dokter = auth()->user();
            $pasien_id = $user->id;

            if ($request->rs_id) {
                $nama_rs_old = $request->nama_rs;
                $rs_id_old = $request->rs_id;

                $rs = HistoriRs::find($rs_id_old);

                if ($rs->nama_rs == $nama_rs_old) {
                    $nama_rs = $rs->nama_rs;
                } else {
                    $nama_rs = HistoriRs::create([
                        'nama_rs' => $request->nama_rs,
                    ]);
                    $nama_rs = $nama_rs->nama_rs;
                }
            } else {
                // $upperNameRS = Str::upper($request->nama_rs);
                $upperNameRS = $request->nama_rs;
                $query = HistoriRs::query();

                if (!empty($upperNameRS)) {
                    $query->where(function ($query) use ($upperNameRS) {
                        $query->whereRaw('nama_rs LIKE ?', ['%' . $upperNameRS . '%']);
                        // $query->whereRaw('UPPER(nama_rs) LIKE ?', ['%' . $upperNameRS . '%']);
                    });
                }

                $rs = $query->first();

                if ($rs) {
                    $nama_rs = $rs->nama_rs;
                } else {
                    $nama_rs = HistoriRs::create([
                        'nama_rs' => $request->nama_rs,
                    ]);
                    $nama_rs = $nama_rs->nama_rs;
                }
            }

            // dd($nama_rs);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            // Begin transaction
            DB::beginTransaction();

            // Generate the surat keterangan berobat number
            // $nomor_surat = 'SKB-' . date('Ymd') . '-' . str_pad(SuratKeteranganBerobat::max('id') + 1, 3, '0', STR_PAD_LEFT);

            if ($dokter->role == 'Dokter') {
                $jabatan_dokter = 'Dokter';
            } elseif ($dokter->role == 'Psikolog') {
                $jabatan_dokter = 'Psikolog';
            } elseif ($dokter->role == 'Perawat') {
                $jabatan_dokter = 'Perawat';
            } else {
                $jabatan_dokter = 'Dokter';
            }

            // Prepare data for SuratKeteranganBerobat
            $data = [
                'dokter_id' => $dokter->id,
                'pasien_id' => $pasien_id,
                'nama_dokter' => $request->nama_dokter,
                'jabatan_dokter' => $jabatan_dokter,
                'jabatan_pasien' => $user->role == 'Mahasiswa' ? 'Mahasiswa' : 'Karyawan',
                'nama_pasien' => $request->nama_pasien,

                'keluhan' => $request->keluhan,
                'diagnosa' => $request->diagnosa,
                'kasus' => $request->kasus,
                'tindakan' => $request->tindakan,

                'nik' => $request->nik,
                'ttl' => $request->ttl,
                'jenis_kelamin' => $request->jenis_kelamin,
                'usia' => $request->usia,
                'no_hp' => $request->no_hp,
                'nama_rs' => $nama_rs,
                // 'nomor_surat' => $nomor_surat,
            ];

            // Add additional data if user role is Mahasiswa
            if ($user->role == 'Mahasiswa') {
                $data = array_merge($data, [
                    'nim' => $user->getCDMI->nim,
                    'no_ruangan' => $user->getCDMI->no_ruangan,
                    'prodi_id' => $user->getCDMI->prodi_id,
                    'blok_id' => $user->getCDMI->blok_id,
                ]);
            }

            // Create SuratKeteranganBerobat record
            $suratRujukan = SuratRujukan::create($data);

            // Check if obat_id and kuantitas are present in the request
            $suratRujukan->save();

            $requestPsikolog = RequestRujukan::find($requestId);

            $requestPsikolog->update([
                'status' => 'completed',
                'rujukan_id' => $suratRujukan->id,
            ]);

            // Commit transaction
            DB::commit();

            return redirect()->route('konseling.hasil-surat-rujukan')->with('success', 'Berhasil Membuat Surat Rujukan');
        } catch (\Exception $th) {
            DB::rollBack();
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal Membuat Surat Rujukan : ' . $th->getMessage());
            } else {
                Log::error('Gagal Membuat Surat Rujukan : ' . $th->getMessage());
                return back()->with('error', 'Gagal Membuat Surat Rujukan : ' . $th->getMessage());
            }
        }
    }

    public function reviewRequestSuratRujukanKonseling($requestId)
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

            return view('kesehatan.form.review-request-surat-rujukan', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Psikolog tidak ditemukan');
            } else {
                return back()->with('error', 'Gagal menampilkan detail Psikolog');
            }
        }
    }

    public function rejectRequestSuratRujukanKonseling(Request $request, $requestId)
    {
        try {
            $request->validate([
                'alasan' => ['required', 'string'],
            ]);

            $requestSurat = RequestRujukan::find($requestId);
            if (!$requestSurat) {
                return back()->with('error', 'Request Rujukan not found');
            }

            DB::beginTransaction();
            $requestSurat->update([
                'status' => 'rejected',
                'alasan_penolakan' => $request->alasan,
            ]);
            DB::commit();

            return back()->with('success', 'Berhasil menolak request rujukan');
        } catch (\Exception $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak request rujukan');
        }
    }

    public function filterRequestSuratRujukanKonseling(Request $request)
    {
        try {
            $input = $request->all();

            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter'] ?? '');

            $query = RequestRujukan::query();

            // Filter based on name, NIM, or keluhan if provided
            if (!empty($upperName)) {
                $query->where(function ($query) use ($upperName) {
                    // cari nim
                    $query->whereHas('dataPsikolog.user.getCDMI', function ($query) use ($upperName) {
                        $query->whereRaw('UPPER(nim) LIKE ?', ['%' . $upperName . '%']);
                    });

                    // cari nama
                    $query->orWhereHas('dataPsikolog.user', function ($query) use ($upperName) {
                        $query->whereRaw('UPPER(name) LIKE ?', ['%' . $upperName . '%']);
                    });

                    // cari keluhan
                    $query->orWhereHas('dataPsikolog', function ($query) use ($upperName) {
                        $query->whereRaw('UPPER(keluhan) LIKE ?', ['%' . $upperName . '%']);
                    });
                });
            }

            $data = $query->with(['dataPsikolog.user.getCDMI', 'suratRujukan'])->orderBy('id', 'desc')->paginate(20);

            $table = view('components.kesehatan.table-request-rujukan', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Request Surat Konseling: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter Request Surat Konseling: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Request Surat Konseling');
            }
        }
    }
}
