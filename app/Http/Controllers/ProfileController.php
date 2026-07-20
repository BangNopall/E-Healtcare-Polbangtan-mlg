<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Blok;
use App\Models\CDMI;
use App\Models\DMTI;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $data = ['user' => $user];

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
        $data['rpd'] = $user->RPD()->get();
        // dd($data);

        return view('profile.edit', $data);
    }



    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateAvatar(Request $request, $user_id): RedirectResponse
    {
        // dd($request->all(), $user_id);
        $request->validate([
            'avatar_url' => ['required', 'image', 'mimes:jpg,jpeg,gif,png', 'max:5000'],
        ]);

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

            return redirect()->route('profile.edit')->with('status', 'avatar-updated');
        } else {
            // Handle the case where the user with the given ID is not found.
            return redirect()->route('profile.edit')->with('status', 'user-not-found');
        }
    }

    public function updateDMTI(Request $request, $user_id)
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
                    })->ignore(optional($user->dmti)->id),
                ],
                'no_bpjs' => [
                    'required',
                    'string',
                    'max:16',
                    Rule::unique('d_m_t_i_s')->where(function ($query) use ($user) {
                        return $query->whereNot('user_id', $user->id);
                    })->ignore(optional($user->dmti)->id),
                ],
                'no_hp' => [
                    'required',
                    'string',
                    'max:16',
                    Rule::unique('d_m_t_i_s')->where(function ($query) use ($user) {
                        return $query->whereNot('user_id', $user->id);
                    })->ignore(optional($user->dmti)->id),
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
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal Update Data Profile: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal Update Data Profile: ' . $th->getMessage());
                return back()->with('error', 'Gagal Update Data Profile: ' . $th->getMessage());
            }
        }
    }
    public function updateCDMI(Request $request, $user_id)
    {
        try {
            // dd($request->all(), $user_id);
            $user = User::find($user_id);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            // Modifikasi nilai input langsung pada request
            $request->merge([
                'prodi_id' => $request->input('prodi_id') === 'Pilih' ? null : $request->input('prodi_id'),
                'blok_id' => $request->input('blok_id') === 'Pilih' ? null : $request->input('blok_id'),
            ]);

            // dd($request->all());

            $request->validate([
                'nim' => [
                    'required',
                    'string',
                    'max:16',
                    Rule::unique('c_d_m_i_s')->where(function ($query) use ($user) {
                        return $query->whereNot('user_id', $user->id);
                    })->ignore(optional($user->cdmi)->id),
                ],
                'prodi_id' => ['required', 'numeric',],
                'blok_id' => ['required', 'numeric'],
                'no_ruangan' => ['required', 'string'],
            ]);

            // dd($request->all());

            DB::beginTransaction();

            $cdmi = CDMI::where('user_id', $user->id)->first();

            if ($cdmi) {
                $cdmi->update([
                    'nim' => $request->nim,
                    'prodi_id' => $request->prodi_id,
                    'blok_id' => $request->blok_id,
                    'no_ruangan' => $request->no_ruangan,
                ]);
            } else {
                CDMI::create([
                    'user_id' => $user->id,
                    'nim' => $request->nim,
                    'prodi_id' => $request->prodi_id,
                    'blok_id' => $request->blok_id,
                    'no_ruangan' => $request->no_ruangan,
                ]);
            }

            $user->update([
                'cdmi' => true, // true
                'cdmi_complete' => true,
            ]);

            DB::commit();

            // Log::info('Nilai cdmi: ' . $user->cdmi);
            // Log::info('Nilai cdmi_complete: ' . $user->cdmi_complete);


            return back()->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal Update Data Profile: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal Update Data Profile: ' . $th->getMessage());
                return back()->with('error', 'Gagal Update Data Profile: ' . $th->getMessage());
            }
        }
    }

    public function storeRPD(Request $request, $user_id): RedirectResponse
    {
        try {
            $request->validate([
                'file_RPD' => ['required', 'file', 'mimes:pdf', 'max:10000'], // max 10MB
            ]);

            // Temukan user berdasarkan user_id
            $user = User::find($user_id);

            if ($user) {
                // Hitung jumlah RPD yang sudah ada untuk user ini
                $fileCount = $user->RPD()->count();

                // Dapatkan nama user dengan spasi diganti dengan _
                $userName = str_replace(' ', '_', $user->name);

                // Buat nama file
                $fileName = 'RPD_' . $userName . '_' . ($fileCount + 1) . '.' . $request->file('file_RPD')->extension();

                // Simpan file baru
                $file = $request->file('file_RPD');
                $file->storeAs('RPD', $fileName, 'public');

                DB::beginTransaction();

                // Tambahkan RPD baru ke user
                $user->RPD()->create([
                    'file_name' => $fileName,
                ]);
                DB::commit();

                return back()->with('success', 'Data berhasil disimpan');
            } else {
                // Handle the case where the user with the given ID is not found.
                return back()->with('error', 'Data tidak ditemukan');
            }
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Data tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Data RPD gagal di kirim : ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Data RPD gagal di kirim : ' . $th->getMessage());
                return back()->with('error', 'Data RPD gagal di kirim');
            }
        }
    }
}
