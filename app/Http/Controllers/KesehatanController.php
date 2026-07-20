<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RekamMedis;
use App\Models\SuratRujukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SuratKeteranganSakit;
use App\Models\SuratKeteranganBerobat;
use App\Models\SuratKeteranganSehat;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KesehatanController extends Controller
{
    public function dashboard()
    {
        // mengambil pasien dengan role mahasiswa dan karyawan
        $user = User::all();
        $skb = SuratKeteranganBerobat::all()->count();
        $sks = SuratKeteranganSakit::all()->count();
        $sr = SuratRujukan::all()->count();
        $skse = SuratKeteranganSehat::all()->count();

        $sk = $skb + $sks + $sr + $skse;

        $rm = RekamMedis::all();

        $rmcount = $rm->count();

        // ambil rekam medis 5 terbaru
        $rm = $rm->sortByDesc('created_at')->take(5);

        $pasien = $user->where('role', 'Mahasiswa')->count() + $user->where('role', 'Karyawan')->count();
        $dokter = $user->where('role', 'Dokter')->count();
        return view('kesehatan.dashboard', compact('pasien', 'dokter', 'sk', 'rmcount', 'rm'));
    }

    public function dataKontrolPasien()
    {
        $data = User::with('PasienRM')
            ->whereIn('role', ['Mahasiswa', 'Karyawan'])
            // ->orWhere('role', 'Karyawan')
            ->orderByRaw('(SELECT COUNT(*) FROM rekam_medis WHERE rekam_medis.pasien_id = users.id) DESC')
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('kesehatan.data-kontrol-pasien', compact('data'));
    }

    public function filterDKP(Request $request)
    {
        try {
            $input = $request->all();

            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter'] ?? '');

            $query = User::with('PasienRM')
                ->whereIn('role', ['Mahasiswa', 'Karyawan']);
                // ->orWhere('role', 'Karyawan');

            if (!empty($upperName)) {
                $query->where(function ($query) use ($upperName) {
                    $query->whereRaw('UPPER(name) LIKE ?', ['%' . $upperName . '%'])
                        ->orWhereRaw('UPPER(email) LIKE ?', ['%' . $upperName . '%']);
                });
            }

            $query = $query->orderByRaw('(SELECT COUNT(*) FROM rekam_medis WHERE rekam_medis.pasien_id = users.id) DESC')
                ->orderBy('name', 'asc');

            $data = $query->paginate(20);

            $table = view('components.kesehatan.table-data-kontrol', compact('data'))->render();

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
}
