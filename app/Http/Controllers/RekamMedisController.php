<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Blok;
use App\Models\CDMI;
use App\Models\DMTI;
use App\Models\User;
use App\Models\Prodi;
use App\Models\HistoriRs;
use App\Exports\laporanRM;
use App\Models\RekamMedis;
use App\Models\DataPsikolog;
use Illuminate\Http\Request;
use App\Models\InventoryAlat;
use App\Models\InventoryObat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryConsumable;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\API\InternalApiController;

class RekamMedisController extends Controller
{
    public function formRiwayatPasien($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak ditemukan');
        }

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

            $pagination = 10;
            // rekam medis dan konsultasi konseling
            // rm
            $rekammedis = RekamMedis::where('pasien_id', $user->id)->orderBy('created_at', 'desc')->paginate($pagination);
            $data['rm'] = $rekammedis;
            // konseling
            $konseling = DataPsikolog::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate($pagination);
            $data['ks'] = $konseling;

            $dataTotal = [
                'totalRm' => RekamMedis::where('pasien_id', $user->id)->count(),
                'totalKs' => DataPsikolog::where('user_id', $user->id)->count(),
            ];

            arsort($dataTotal);
            $keyPagination = key($dataTotal);
            if ($keyPagination == 'totalRm') {
                $keyPagination = 'rm';
            } elseif ($keyPagination == 'totalKs') {
                $keyPagination = 'ks';
            }

            return view('kesehatan.form.riwayat-pasien', $data, compact('keyPagination'));
        } else {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak memiliki akses ke halaman ini');
        }
    }

    public function storeRekamMedis(Request $request, $user_id)
    {
        try {
            $validatedData = $request->validate([
                'keluhan' => 'required',
                'pemeriksaan' => 'required',
                'diagnosa' => 'required',

                // Obat
                'obat_id' => 'nullable|array|min:1',
                'obat_id.*' => 'nullable|exists:inventory_obats,id',
                'nama_obat' => 'nullable|array|min:1',
                'nama_obat.*' => 'nullable|string',
                'jumlah_obat.*' => 'nullable|min:1',

                // Alat
                'alat_id' => 'nullable|array|min:1',
                'alat_id.*' => 'nullable',
                'nama_alat' => 'nullable|array|min:1',
                'nama_alat.*' => 'nullable|string',
                'jumlah_alat.*' => 'nullable|min:1',
                'identity_alat' => 'nullable|array|min:1',
                'identity_alat.*' => 'nullable|string',

                // Rumah Sakit
                'rawatjalan' => 'nullable|string',
                'rs_name_rujukan' => 'nullable|string',
                'rs_name_rawatinap' => 'nullable|string',
            ]);

            $pasien = User::find($user_id);
            $dokter =  Auth::user();

            // Memeriksa apakah user memiliki role yang sesuai
            if (!in_array($dokter->role, ['Dokter', 'Psikolog', 'Perawat', 'Admin'])) {
                // Redirect kembali ke halaman sebelumnya dengan pesan error
                throw new \InvalidArgumentException("Anda tidak memiliki akses untuk membuat rekam medis.");
            }
            if (!$pasien) {
                throw new \InvalidArgumentException("User dengan ID $user_id tidak ditemukan.");
            }
            if (!$dokter) {
                throw new \InvalidArgumentException("Tidak dapat mengambil data dokter yang sedang login.");
            }

            // validation Obat
            $obatIds = $request->input('obat_id', []);
            $namaObats = $request->input('nama_obat', []);
            $jumlahObats = $request->input('jumlah_obat', []);

            if (count($obatIds) !== count($namaObats) || count($obatIds) !== count($jumlahObats)) {
                return back()->with('error', 'obat_id, nama_obat, dan jumlah_obat tidak sesuai.')->withInput();
            }

            $invalidObatIndices = [];
            $removedObatNames = [];

            foreach ($jumlahObats as $key => $jumlah) {
                if ($jumlah < 1) {
                    $invalidObatName = $namaObats[$key];
                    $invalidObatIndices[] = $key;
                    $removedObatNames[] = $invalidObatName;
                }
            }

            if (!empty($invalidObatIndices)) {
                // Reverse sort the indices to avoid shifting problems while splicing
                rsort($invalidObatIndices);

                foreach ($invalidObatIndices as $index) {
                    array_splice($obatIds, $index, 1);
                    array_splice($namaObats, $index, 1);
                    array_splice($jumlahObats, $index, 1);
                }

                $errorMessage = 'Jumlah obat minimal adalah 1. Ada kesalahan input pada obat: ' . implode(', ', $removedObatNames) . '. Silahkan inputkan ulang untuk obat tersebut.';
                // Update the request with corrected arrays
                $request->merge([
                    'obat_id' => $obatIds,
                    'nama_obat' => $namaObats,
                    'jumlah_obat' => $jumlahObats,
                ]);
                return back()->with('error', $errorMessage)->withInput($request->all());
            }

            // validation Alat
            $alatIds = $request->input('alat_id', []);
            $namaAlats = $request->input('nama_alat', []);
            $jumlahAlats = $request->input('jumlah_alat', []);
            $identityAlats = $request->input('identity_alat', []);

            if (count($alatIds) !== count($namaAlats) || count($alatIds) !== count($jumlahAlats) || count($alatIds) !== count($identityAlats)) {
                return back()->with('error', 'alat_id, nama_alat, jumlah_alat, dan identity_alat tidak sesuai.')->withInput();
            }

            $invalidAlatIndices = [];
            $removedAlatNames = [];
            foreach ($jumlahAlats as $key => $jumlah) {
                if ($jumlah < 1) {
                    $invalidAlatName = $namaAlats[$key];
                    $invalidAlatIndices[] = $key;
                    $removedAlatNames[] = $invalidAlatName;
                }
            }

            if (!empty($invalidAlatIndices)) {
                // Reverse sort the indices to avoid shifting problems while splicing
                rsort($invalidAlatIndices);

                foreach ($invalidAlatIndices as $index) {
                    array_splice($alatIds, $index, 1);
                    array_splice($namaAlats, $index, 1);
                    array_splice($jumlahAlats, $index, 1);
                    array_splice($identityAlats, $index, 1);
                }

                $errorMessage = 'Jumlah alat minimal adalah 1. Ada kesalahan input pada alat: ' . implode(', ', $removedAlatNames) . '. Silahkan inputkan ulang untuk alat tersebut.';
                // Update the request with corrected arrays
                $request->merge([
                    'alat_id' => $alatIds,
                    'nama_alat' => $namaAlats,
                    'jumlah_alat' => $jumlahAlats,
                    'identity_alat' => $identityAlats,
                ]);
                return back()->with('error', $errorMessage)->withInput($request->all());
            }

            // validation Rumah Sakit
            // Mendapatkan inputan rumah sakit dari request
            $rawatJalan = $request->input('rawatjalan', '');
            $rsNameRujukan = $request->input('rs_name_rujukan', '');
            $rsNameRawatinap = $request->input('rs_name_rawatinap', '');

            // Mengambil daftar rumah sakit dari API atau database
            $internalApiController = new InternalApiController();
            $daftarRumahSakit = $internalApiController->daftarRS()->original;

            // Mengambil daftar nama rumah sakit dan menormalkan ke huruf kecil
            $namaRumahSakitTerdaftar = array_map('strtolower', array_column($daftarRumahSakit, 'nama_rs'));

            // Fungsi untuk memeriksa apakah nama rumah sakit sudah terdaftar
            function isRumahSakitTerdaftar($namaRs, $daftarNamaRs)
            {
                return in_array(strtolower($namaRs), $daftarNamaRs);
            }


            DB::beginTransaction();
            if ($validatedData['rs_name_rujukan'] != null) {
                // Memeriksa apakah rumah sakit rujukan sudah terdaftar
                if (!isRumahSakitTerdaftar($rsNameRujukan, $namaRumahSakitTerdaftar)) {
                    // Daftarkan rumah sakit baru jika belum terdaftar
                    HistoriRs::create(['nama_rs' => $rsNameRujukan]);
                }
            }

            if ($validatedData['rs_name_rawatinap'] != null) {
                // Memeriksa apakah rumah sakit rawat inap sudah terdaftar
                if (!isRumahSakitTerdaftar($rsNameRawatinap, $namaRumahSakitTerdaftar)) {
                    // Daftarkan rumah sakit baru jika belum terdaftar
                    HistoriRs::create(['nama_rs' => $rsNameRawatinap]);
                }
            }

            // insert data to Databased Rekam Medis
            // Menyiapkan data untuk tindakan (obat dan alat)
            $tindakan = [
                'obat_id' => [],
                'nama_obat' => [],
                'jumlah_obat' => [],
                'alat_id' => [],
                'nama_alat' => [],
                'jumlah_alat' => [],
                'identity_alat' => [],
            ];

            // Memproses tindakan obat jika ada
            if ($request->has('obat_id')) {
                $withObat = 1;
                foreach ($validatedData['obat_id'] as $key => $obatId) {
                    $obat = InventoryObat::find($obatId);
                    if (!$obat) {
                        throw new \InvalidArgumentException("Obat dengan ID $obatId tidak ditemukan.");
                    }
                    $tindakan['obat_id'][] = $obatId;
                    $tindakan['nama_obat'][] = $validatedData['nama_obat'][$key];
                    $tindakan['jumlah_obat'][] = $validatedData['jumlah_obat'][$key];
                    // Kurangi stok obat yang digunakan
                    $obat->decrement('stok', $validatedData['jumlah_obat'][$key]);
                }
            }

            // Memproses tindakan alat jika ada
            if ($request->has('alat_id')) {
                $withAlat = 1;
                foreach ($validatedData['alat_id'] as $key => $alatId) {
                    // Periksa tipe alat berdasarkan identity_alat
                    if ($validatedData['identity_alat'][$key] === 'alat') {
                        $alat = InventoryAlat::find($alatId);
                        if (!$alat) {
                            throw new \InvalidArgumentException("Alat dengan ID $alatId tidak ditemukan.");
                        }
                    } elseif ($validatedData['identity_alat'][$key] === 'consumable') {
                        // Implementasi untuk InventoryConsumable jika diperlukan
                        $alat = InventoryConsumable::find($alatId);
                    } else {
                        throw new \InvalidArgumentException("Identitas alat tidak valid.");
                    }

                    $tindakan['alat_id'][] = $alatId;
                    $tindakan['nama_alat'][] = $validatedData['nama_alat'][$key];
                    $tindakan['jumlah_alat'][] = $validatedData['jumlah_alat'][$key];
                    $tindakan['identity_alat'][] = $validatedData['identity_alat'][$key];
                    // Proses penggunaan alat atau consumable lainnya jika diperlukan
                    // Contoh: Kurangi stok alat yang digunakan
                    $alat->decrement('stok', $validatedData['jumlah_alat'][$key]);
                }
            }

            // Menyiapkan data untuk rekam medis
            $rekamMedisData = [
                'tanggal' => now(),
                'dokter_id' => $dokter->id,
                'pasien_id' => $pasien->id,
                'keluhan' => $validatedData['keluhan'],
                'pemeriksaan' => $validatedData['pemeriksaan'],
                'diagnosa' => $validatedData['diagnosa'],
                'tindakan' => json_encode($tindakan),
                'withObat' => $withObat,
                'withAlat' => $withAlat,
                'rawatjalan' => $validatedData['rawatjalan'],
                'rs_name_rujukan' => $validatedData['rs_name_rujukan'],
                'rs_name_rawatinap' => $validatedData['rs_name_rawatinap'],
            ];

            // Simpan data rekam medis ke dalam database
            RekamMedis::create($rekamMedisData);

            DB::commit();

            return redirect()->route('kesehatan.riwayat-pasien', $pasien->id)->with('success', 'Rekam medis berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())
                    ->withInput($request->all())
                    ->with('error', 'Gagal membuat Rekam medis: ' . $th->getMessage());
            } else {
                Log::error('Gagal membuat Rekam medis: ' . $th->getMessage());
                return back()->with('error', 'Gagal membuat Rekam medis: ' . $th->getMessage())
                    ->withInput($request->all());
            }
        }
    }


    public function detailRekamMedis($rm_id)
    {
        $rm = RekamMedis::find($rm_id);
        if (!$rm) {
            return redirect()->route('kesehatan.kamera')->with('error', 'Rekam medis tidak ditemukan');
        }
        $user = User::find($rm->pasien_id);
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
            // encode json
            $data['rm'] = $rm;
            $data['tindakan'] = json_decode($rm->tindakan, true);

            return view('kesehatan.form.detail-rm', $data);
        } else {
            return redirect()->route('kesehatan.kamera')->with('error', 'User tidak memiliki akses ke halaman ini');
        }
    }

    public function filterRm(Request $request)
    {
        try {
            $input = $request->all();
            if ($input['daysrm'] === 'Filter') {
                $input['daysrm'] = null;
            }

            $validatedData = Validator::make($input, [
                'filter-rm' => 'nullable|string',
                'daysrm' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter-rm'] ?? '');
            $selectedDays = $validatedData['daysrm'] ?? '';

            $user = $request->userid;

            // mengambil data query rekam medis berdasarkan user id
            $query = RekamMedis::where('pasien_id', $user);

            if (!empty($upperName)) {
                $query->where(function ($query) use ($upperName) {
                    $query->whereRaw('UPPER(keluhan) LIKE ?', ['%' . $upperName . '%'])
                        ->orWhereRaw('UPPER(diagnosa) LIKE ?', ['%' . $upperName . '%']);
                });
            }
            if (!empty($selectedDays)) {
                switch ($selectedDays) {
                    case '7day':
                        $query->whereBetween('created_at', [now()->subDays(7), now()]);
                        break;
                    case '14day':
                        $query->whereBetween('created_at', [now()->subDays(14), now()]);
                        break;
                    case '30day':
                        $query->whereBetween('created_at', [now()->subDays(30), now()]);
                        break;
                }
            }

            $data = $query->orderBy('created_at', 'desc')->paginate(10);
            $table = view('components.kesehatan.table-rm', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Throwable $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal filter Rekam Medis: ' . $th->getMessage());
            } else {
                Log::error('Gagal filter Rekam Medis: ' . $th->getMessage());
                return back()->with('error', 'Gagal filter Rekam Medis');
            }
        }
    }
    public function filterKs(Request $request)
    {
        try {
            $input = $request->all();
            if ($input['daysks'] === 'Filter') {
                $input['daysks'] = null;
            }

            $validatedData = Validator::make($input, [
                'filter-ks' => 'nullable|string',
                'daysks' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter-ks'] ?? '');
            $selectedDays = $validatedData['daysks'] ?? '';

            $user = $request->userid;

            // mengambil data query rekam medis berdasarkan user id
            $query = DataPsikolog::where('user_id', $user);

            if (!empty($upperName)) {
                $query->where(function ($query) use ($upperName) {
                    $query->whereRaw('UPPER(keluhan) LIKE ?', ['%' . $upperName . '%'])
                        ->orWhereRaw('UPPER(diagnosa) LIKE ?', ['%' . $upperName . '%']);
                });
            }
            if (!empty($selectedDays)) {
                switch ($selectedDays) {
                    case '7day':
                        $query->whereBetween('created_at', [now()->subDays(7), now()]);
                        break;
                    case '14day':
                        $query->whereBetween('created_at', [now()->subDays(14), now()]);
                        break;
                    case '30day':
                        $query->whereBetween('created_at', [now()->subDays(30), now()]);
                        break;
                }
            }

            $data = $query->orderBy('created_at', 'desc')->paginate(10);
            $table = view('components.kesehatan.table-konseling', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Throwable $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal filter Rekam Medis: ' . $th->getMessage());
            } else {
                Log::error('Gagal filter Rekam Medis: ' . $th->getMessage());
                return back()->with('error', 'Gagal filter Rekam Medis');
            }
        }
    }

    public function tipeSurat(Request $request, $rm_id)
    {
        // Validasi input
        $request->validate([
            'tipe' => 'required|string'
        ]);

        // Ambil nilai tipe dari request
        $tipe = $request->input('tipe');

        // Buat URL berdasarkan nilai tipe
        switch ($tipe) {
            case 'surat_keterangan_obat':
                $url = route('kesehatan.form.surat-keterangan-obat', ['id' => $rm_id]);
                break;
            case 'surat_keterangan_sakit':
                $url = route('kesehatan.form.surat-keterangan-sakit', ['id' => $rm_id]);
                break;
            case 'surat_keterangan_sehat':
                $url = route('kesehatan.form.surat-keterangan-sehat', ['id' => $rm_id]);
                break;
            case 'surat_rujukan':
                $url = route('kesehatan.form.surat-rujukan', ['id' => $rm_id]);
                break;
            default:
                // Jika tipe tidak dikenali, kembali ke halaman sebelumnya dengan pesan error
                return redirect()->back()->withErrors(['tipe' => 'Tipe surat tidak valid.']);
        }

        // Redirect ke URL yang ditentukan
        return redirect($url);
    }

    public function downloadRekamMedis($id)
    {
        try {
            if (!$id) {
                return back()->with('error', 'ID user tidak ditemukan');
            }
            $user = User::find($id);
            $rekamMedis = RekamMedis::where('pasien_id', $id)->get();

            foreach ($rekamMedis as $rm) {
                $rm->tindakan = json_decode($rm->tindakan, true);
            }

            // dd($rm);

            $pdf = Pdf::loadView('print.pasien-rm', compact('rekamMedis', 'user'));

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name . 'RM',) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return view('print.pasien-rm', compact('rekamMedis', 'user'));
        } catch (\Throwable $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal unduh RM: ' . $th->getMessage());
            } else {
                Log::error('Gagal unduh RM: ' . $th->getMessage());
                return back()->with('error', 'Gagal unduh RM');
            }
        }
    }

    public function laporanRekamMedis()
    {
        return view('kesehatan.laporan-rekammedis');
    }

    public function printLaporanRekamMedis(Request $request)
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

        $rekammedis = RekamMedis::whereMonth('created_at', $month)->whereYear('created_at', $year)
            ->with(['pasien.getDMTI'])
            ->get();

        // dd($rekammedis);

        foreach ($rekammedis as $rm) {
            $rm->tindakan = json_decode($rm->tindakan, true);
        }

        try {
            if ($request->submit == 'pdf') {
                $pdf = Pdf::loadView('print.laporan-rm', compact('rekammedis', 'monthName'))->setPaper('a3', 'landscape');
                $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', 'Laporan Rekam Medis_' . $monthName) . '.pdf';
                $filePath = storage_path('app/public/' . $fileName);

                // Simpan file pdf
                $pdf->save($filePath);
                // Mengembalikan URL atau path ke file PDF yang baru disimpan
                return response()->download($filePath)->deleteFileAfterSend(true);
            }
            if ($request->submit == 'excel') {
                return Excel::download(new laporanRM($rekammedis, $monthName), 'Laporan Rekam Medis_' . $monthName . '.xlsx');
            }
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Laporan Rekam Medis: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Laporan Rekam Medis:' . $th->getMessage());
        }
    }
}
