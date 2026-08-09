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



class MahasiswaManagementController extends Controller
{
    public function index()
    {
        $data = User::where('role', 'Mahasiswa')->paginate(20);
        // dd($data);
        return view('lainnya.mahasiswa.data-mahasiswa', compact('data'));
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

            $lowPassword = strtolower($request->name);

            $notHashedPassword = Str::substr($lowPassword, 0, 5);

            $createPassword = Hash::make($notHashedPassword);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'Mahasiswa',
                'password' => $createPassword,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data mahasiswa baru berhasil dibuat, Password User Tersebut Adalah : ' . $notHashedPassword);
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Mahasiswa tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Mahasiswa gagal disimpan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Mahasiswa gagal disimpan : ' . $th->getMessage());
                return back()->with('error', 'Data Mahasiswa gagal disimpan : ' . $th->getMessage());
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
            if ($user->cdmi == true) {
                $cdmi = CDMI::where('user_id', $user->id)->first();
                if ($cdmi) {
                    $data['cdmi'] = $cdmi;
                }
            }
            $data['prodis'] = Prodi::all();
            $data['bloks'] = Blok::all();
            // dd($data);

            return view('lainnya.mahasiswa.lihat', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Mahasiswa tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Mahasiswa gagal ditampilkan : ' . $th->getMessage());
                return back()->with('error', 'Data Mahasiswa gagal ditampilkan');
            }
        }
    }

    public function destroy($user)
    {
        try {
            $user = User::find($user);

            if (!$user) {
                throw new ModelNotFoundException();
            }

            // store data
            DB::beginTransaction();

            $user->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data gagal dihapus : ' . $th->getMessage());
                return back()->with('error', 'Data gagal dihapus');
            }
        }
    }


    public function updateAvatar(Request $request, $user_id): RedirectResponse
    {
        try {
            $request->validate([
                'avatar_url' => ['required', 'image', 'mimes:jpg,jpeg,gif,png', 'max:5000'],
            ]);

            DB::beginTransaction();

            $user = User::find($user_id);

            // dd($user);

            if ($user) {
                if ($user->avatar_url) {
                    Storage::delete('/public/images/' . $user->avatar_url);
                }

                $file = $request->file('avatar_url');
                $avatarName = Str::random(24) . '.' . $request->file('avatar_url')->extension();
                $file->storeAs('images', $avatarName, 'public');

                $user->update([
                    'avatar_url' => $avatarName,
                ]);

                DB::commit();

                return redirect()->back()->with('success', 'Avatar berhasil diubah');
            } else {
                // Handle the case where the user with the given ID is not found.
                return redirect()->back()->with('error', 'Data User tidak ditemukan');
            }
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Mahasiswa tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Mahasiswa gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Mahasiswa gagal update : ' . $th->getMessage());
                return back()->with('error', 'Data Mahasiswa gagal update');
            }
        }
    }

    public function hapusAvatar(Request $request, $user_id)
    {
        try {
            $user = User::find($user_id);

            if ($user) {
                DB::beginTransaction();

                if ($user->avatar_url) {
                    Storage::delete('/public/images/' . $user->avatar_url);
                }

                $user->update([
                    'avatar_url' => null,
                ]);

                DB::commit();

                return redirect()->back()->with('success', 'Avatar berhasil dihapus');
            } else {
                // Handle the case where the user with the given ID is not found.
                return redirect()->back()->with('error', 'Data User tidak ditemukan');
            }
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Mahasiswa tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Mahasiswa gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Mahasiswa gagal update : ' . $th->getMessage());
                return back()->with('error', 'Data Mahasiswa gagal update');
            }
        }
    }

    public function updateDataMahasiswa(Request $request, $user_id)
    {
        try {
            $user = User::find($user_id);
            // dd($request->all());

            if (!$user) {
                return back()->with('error', 'Data Mahasiswa tidak ditemukan');
            } else {
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->whereNot('email', $user->email)->ignore(optional($user)->id)],
                    'role' => ['required', 'string', Rule::in(['Mahasiswa', 'Dokter', 'Psikolog', 'Karyawan', 'Admin', 'Perawat'])],
                ]);

                DB::beginTransaction();

                if ($request->role == 'Mahasiswa') {
                    // check DMTI
                    $DMTI = DMTI::where('user_id', $user->id)->first();
                    // check CDMI
                    $CDMI = CDMI::where('user_id', $user->id)->first();

                    if (!$DMTI) {
                        $user->update([
                            'dmti' => 1,
                            'dmti_complete' => 0,
                        ]);
                    } elseif ($DMTI) {
                        $user->update([
                            'dmti' => 1,
                            'dmti_complete' => 1,
                        ]);
                    }

                    if (!$CDMI) {
                        $user->update([
                            'cdmi' => 1,
                            'cdmi_complete' => 0,
                        ]);
                    } elseif ($CDMI) {
                        $user->update([
                            'cdmi' => 1,
                            'cdmi_complete' => 1,
                        ]);
                    }
                }

                if ($request->role == 'Karyawan') {
                    // check DMTI
                    $DMTI = DMTI::where('user_id', $user->id)->first();

                    if (!$DMTI) {
                        $user->update([
                            'dmti' => 1,
                            'dmti_complete' => 0,
                            'cdmi' => 0,
                            'cdmi_complete' => 0,
                        ]);
                    } elseif ($DMTI) {
                        $user->update([
                            'dmti' => 1,
                            'dmti_complete' => 1,
                            'cdmi' => 0,
                            'cdmi_complete' => 0,
                        ]);
                    }
                }

                if ($request->role == 'Admin' || $request->role == 'Dokter' || $request->role == 'Psikolog' || $request->role == 'Perawat') {
                    $user->update([
                        'dmti' => 0,
                        'dmti_complete' => 0,
                        'cdmi' => 0,
                        'cdmi_complete' => 0,
                    ]);
                }
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'role' => $request->role, // Ensure role is updated
                ]);

                DB::commit();

                return redirect()->back()->with('success', 'Data User berhasil diubah');
            }
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data User tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data User gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data User gagal update : ' . $th->getMessage());
                return back()->with('error', 'Data User gagal update');
            }
        }
    }

    public function updateDataDMTI(Request $request, $user_id)
    {
        try {
            $user = User::find($user_id);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            $request->merge([
                'jenis_kelamin' => $request->input('jenis_kelamin') === 'Pilih' ? null : $request->input('jenis_kelamin'),
                'golongan_darah' => $request->input('golongan_darah') === 'Pilih' ? null : $request->input('golongan_darah'),
            ]);

            $request->validate([
                'nik' => [
                    'required',
                    'string',
                    'max:16',
                    Rule::unique('d_m_t_i_s')->where(function ($query) use ($user) {
                        return $query->whereNot('user_id', $user->id);
                    }),
                ],
                'no_bpjs' => [
                    'required',
                    'string',
                    'max:16',
                    Rule::unique('d_m_t_i_s')->where(function ($query) use ($user) {
                        return $query->whereNot('user_id', $user->id);
                    }),
                ],
                'no_hp' => [
                    'required',
                    'string',
                    'max:16',
                    Rule::unique('d_m_t_i_s')->where(function ($query) use ($user) {
                        return $query->whereNot('user_id', $user->id);
                    }),
                ],
                'tempat_kelahiran' => ['required', 'string'],
                'tanggal_lahir' => ['required', 'date'],
                'jenis_kelamin' => ['required', 'string', 'in:pria,wanita'],
                'usia' => ['required', 'numeric'],
                'golongan_darah' => ['required', 'string', 'in:A+,B+,AB+,O+,A-,B-,AB-,O-'],
            ]);

            DB::beginTransaction();

            $dmti = DMTI::where('user_id', $user->id)->first();

            if ($dmti) {
                $dmti->update([
                    'tempat_kelahiran' => $request->tempat_kelahiran,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'usia' => $request->usia,
                    'golongan_darah' => $request->golongan_darah,
                ]);
            } else {
                DMTI::create([
                    'user_id' => $user->id,
                    'nik' => $request->nik,
                    'no_bpjs' => $request->no_bpjs,
                    'no_hp' => $request->no_hp,
                    'tempat_kelahiran' => $request->tempat_kelahiran,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'usia' => $request->usia,
                    'golongan_darah' => $request->golongan_darah,
                ]);
            }

            $user->update([
                'dmti' => true, // true
                'dmti_complete' => true,
            ]);

            DB::commit();

            return back()->with('success', 'Data berhasil disimpan');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data User tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data User gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data User gagal update : ' . $th->getMessage());
                return back()->with('error', 'Data User gagal update');
            }
        }
    }

    public function updateDataPassword(Request $request, $user_id)
    {
        try {
            $user = User::find($user_id);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            DB::beginTransaction();

            $validated = $request->validate([
                'password_confirmation' => ['required'],
                'password' => ['required', 'confirmed'],
            ]);

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();
            return back()->with('success', 'Password berhasil diubah');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'User not found');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Validation failed');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Failed to update password: ' . $th->getMessage());
                return back()->with('error', 'Failed to update password');
            }
        }
    }

    public function filterMahasiswa(Request $request)
    {
        try {
            $input = $request->all();

            if ($input['prodi'] === 'Pilih') {
                $input['prodi'] = null;
            }

            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
                'prodi' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter'] ?? '');
            $prodi = strtoupper($validatedData['prodi'] ?? '');

            $query = User::query();

            $query->where('role', 'Mahasiswa');

            // Filter based on name or email if provided
            if (!empty($upperName)) {
                $query->where(function ($query) use ($upperName) {
                    $query->whereRaw('UPPER(name) LIKE ?', ['%' . $upperName . '%'])
                        ->orWhereRaw('UPPER(email) LIKE ?', ['%' . $upperName . '%']);
                });
            }

            // Filter based on prodi if provided
            if (!empty($prodi)) {
                $query->whereHas('getCDMI', function ($query) use ($prodi) {
                    $query->where('cdmi_complete', true)
                        ->whereHas('prodi', function ($query) use ($prodi) {
                            $query->whereRaw('UPPER(name) LIKE ?', ['%' . $prodi . '%']);
                        });
                });
            }

            // Log::info('Filter Mahasiswa: ' . $query->toSql());

            $data = $query->orderBy('name', 'asc')->paginate(20);

            $table = view('components.lainnya.table-data-mahasiswa', compact('data'))->render();

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