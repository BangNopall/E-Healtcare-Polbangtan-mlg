<?php

namespace App\Http\Controllers\API;

use DB;
use App\Models\User;
use App\Models\DataPsikolog;
use Illuminate\Http\Request;
use App\Models\BimbinganSenso;
use App\Models\FeedbackBimbingan;
use Illuminate\Routing\Controller;

class InternalApiController extends Controller
{
    public function get_user()
    {
        $data = User::select('id', 'name')->whereNot('role', 'Admin')->orderBy('name', 'asc')->get()->toArray();
        return response()->json($data, 200);
    }

    public function userNoSenso()
    {
        $data = User::where('senso', 0)->where('role', 'Mahasiswa')->orderBy('name', 'asc')->get()->toArray();
        return response()->json($data, 200);
    }

    public function userNoSensoNoAnakAsuh()
    {
        // Inisialisasi array data
        $data = [];

        // Ambil semua user dengan kondisi senso = 0 dan role = Mahasiswa
        $users = User::where('senso', 0)->where('role', 'Mahasiswa')->orderBy('name', 'asc')->get();

        // Iterasi setiap user
        foreach ($users as $user) {
            // Cek apakah user sudah terdaftar di BimbinganSenso
            $bimbinganSenso = BimbinganSenso::where('siswa_id', $user->id)->first();

            // Jika tidak ditemukan, tambahkan user ke dalam array data
            if (!$bimbinganSenso) {
                $data[] = $user;
            }
        }

        // Kembalikan response JSON dengan data user
        return response()->json($data, 200);
    }

    public function getKonseling()
    {
        $fb = FeedbackBimbingan::all()->count();
        $ks = DataPsikolog::all()->count();
        // buatkan result
        $data = [
            'fb' => $fb,
            'ks' => $ks,
        ];

        return response()->json($data, 200);
    }
}
