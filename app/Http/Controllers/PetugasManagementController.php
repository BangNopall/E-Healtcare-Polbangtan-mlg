<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use App\Models\CDMI;
use App\Models\DMTI;
use App\Models\InventoryObat;
use App\Models\ObatLog;
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


class PetugasManagementController extends Controller
{
    public function index()
    {
        $roles = ['Admin', 'Dokter', 'Psikolog', 'Perawat'];
        $data = User::whereIn('role', $roles)->paginate(20);
        return view('lainnya.petugas.data-petugas', compact('data'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
                'role' => ['required', 'string', Rule::in(['Dokter', 'Psikolog', 'Admin', 'Perawat'])],
            ]);

            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make('password'),
                'cdmi' => 0,
                'cdmi_complete' => 0,
                'dmti' => 0,
                'dmti_complete' => 0,
            ]);

            DB::commit();

            return redirect()->route('lainnya.petugas.index')->with('success', 'Data Karyawan baru berhasil dibuat, Password User Tersebut Adalah : ' . 'password');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Petugas tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Petugas gagal disimpan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Petugas gagal disimpan : ' . $th->getMessage());
                return back()->with('error', 'Data Petugas gagal disimpan');
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
            return view('lainnya.petugas.lihat', $data);
        } catch (\Exception $th) {
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Petugas tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Petugas gagal ditampilkan : ' . $th->getMessage());
                return back()->with('error', 'Data Petugas gagal ditampilkan');
            }
        }
    }

    public function destroy($user_id)
    {
        try {
            $user = User::find($user_id);

            if (!$user) {
                throw new ModelNotFoundException();
            }
            // Temukan admin default
            $admin = User::where('role', 'Admin')->first();

            if (!$admin) {
                return back()->with('error', 'Admin default tidak ditemukan');
            }

            // store data
            DB::beginTransaction();

            // Perbarui createdBy menjadi admin default
            InventoryObat::where('createdBy', $user_id)->update(['createdBy' => $admin->id]);

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
                return back()->with('error', 'Data gagal dihapus : ' . $th->getMessage());
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

                return redirect()->route('lainnya.petugas.show', $user_id)->with('status', 'avatar-updated');
            } else {
                // Handle the case where the user with the given ID is not found.
                return redirect()->route('lainnya.petugas.show', $user_id)->with('status', 'user-not-found');
            }
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Petugas tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Petugas gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Petugas gagal update : ' . $th->getMessage());
                return back()->with('error', 'Data Petugas gagal update');
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

                return redirect()->route('lainnya.petugas.show', $user_id)->with('status', 'avatar-deleted');
            } else {
                // Handle the case where the user with the given ID is not found.
                return redirect()->route('lainnya.petugas.show', $user_id)->with('status', 'user-not-found');
            }
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Petugas tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Petugas gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Petugas gagal update : ' . $th->getMessage());
                return back()->with('error', 'Data Petugas gagal update');
            }
        }
    }

    public function updateDataPetugas(Request $request, $user_id)
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

                if ($request->role == 'Admin' || $request->role == 'Dokter' || $request->role == 'Psikolog'|| $request->role == 'Perawat') {
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

                return redirect()->route('lainnya.petugas.show', $user_id)->with('status', 'data-Petugas-updated');
            }
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data Petugas tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data Petugas gagal dihapus');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data Petugas gagal update : ' . $th->getMessage());
                return back()->with('error', 'Data Petugas gagal update');
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

    public function filterPetugas(Request $request)
    {
        try {
            $input = $request->all();

            $validatedData = Validator::make($input, [
                'filter' => 'nullable|string',
            ])->validate();

            $upperName = strtoupper($validatedData['filter'] ?? '');

            $query = User::query();

            $query->whereIn('role', ['Admin', 'Dokter', 'Psikiater']);

            // Filter based on name or email if provided
            if (!empty($upperName)) {
                $query->where(function ($query) use ($upperName) {
                    $query->whereRaw('UPPER(name) LIKE ?', ['%' . $upperName . '%'])
                        ->orWhereRaw('UPPER(email) LIKE ?', ['%' . $upperName . '%']);
                });
            }

            $data = $query->orderBy('updated_at', 'desc')->paginate(20);

            $table = view('components.lainnya.table-data-Petugas', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Petugas: ' . $th->getMessage());
            } else {
                Log::error('Gagal memfilter Petugas: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Petugas');
            }
        }
    }
}
