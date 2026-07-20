<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use App\Models\CDMI;
use App\Models\DMTI;
use App\Models\Prodi;
use App\Models\User;
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

class KaryawanManagementController extends Controller
{
    public function index()
    {
        $data = User::where('role', 'Karyawan')->paginate(20);
        return view('lainnya.karyawan.data-karyawan', compact('data'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                // 'role' => ['required', 'string', Rule::in(['Mahasiswa', 'Dokter', 'Psikiater', 'Karyawan', 'Admin'])],
            ]);

            $email = $request->email;

            if (preg_match('/[A-Z]/', $email)) {
                return back()->withInput()->with('error', 'Email harus huruf kecil');
            }

            DB::beginTransaction();

            // $lowPassword = strtolower($request->name);

            // $notHashedPassword = Str::substr($lowPassword, 0, 5);

            $createPassword = Hash::make('password');

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'Karyawan',
                'password' => $createPassword,
                'cdmi' => 0,
                'cdmi_complete' => 0,
                'dmti' => 1,
                'dmti_complete' => 0,
            ]);

            DB::commit();

            // return redirect()->route('lainnya.karyawan.index')->with('success', 'Data Karyawan baru berhasil dibuat, Password User Tersebut Adalah : ' . $notHashedPassword);
            return redirect()->route('lainnya.karyawan.index')->with('success', 'Data Karyawan baru berhasil dibuat, Password User Tersebut Adalah : ' . 'password');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Karyawan tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Karyawan gagal disimpan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Karyawan gagal disimpan : ' . $th->getMessage());
                return back()->with('error', 'Data Karyawan gagal disimpan');
            }
        }
    }

    public function show($user)
    {
        try {
            $user = User::find($user);
            $data['user'] = $user;
            if ($user->dmti == true) {
                $dmti = DMTI::where('user_id', $user->id)->first();
                if ($dmti) {
                    $data['dmti'] = $dmti;
                }
            }
            return view('lainnya.karyawan.lihat', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Karyawan tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Karyawan gagal ditampilkan : ' . $th->getMessage());
                return back()->with('error', 'Data Karyawan gagal ditampilkan');
            }
        }
    }

    public function filterKaryawan(Request $request)
    {
        try {
            $input = $request->all();

            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter'] ?? '');

            $query = User::query();

            $query->where('role', 'Karyawan');

            // Filter based on name or email if provided
            if (!empty($upperName)) {
                $query->where(function ($query) use ($upperName) {
                    $query->whereRaw('UPPER(name) LIKE ?', ['%' . $upperName . '%'])
                        ->orWhereRaw('UPPER(email) LIKE ?', ['%' . $upperName . '%']);
                });
            }

            $data = $query->orderBy('updated_at', 'desc')->paginate(20);

            $table = view('components.lainnya.table-data-karyawan', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Karyawan: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter Karyawan: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Karyawan');
            }
        }
    }
}
