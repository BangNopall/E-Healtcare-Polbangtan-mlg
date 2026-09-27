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
        $data = User::select('id', 'name', 'nim')->where('senso', 0)->where('role', 'Mahasiswa')->orderBy('name', 'asc')->get()->toArray();
        return response()->json($data, 200);
    }

    public function userNoSensoNoAnakAsuh()
    {
        $users = User::select('id', 'name', 'nim')
            ->where('senso', 0)
            ->where('role', 'Mahasiswa')
            ->whereDoesntHave('bimbinganSenso')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($users, 200);
    }

    public function getKonseling()
    {
        $data = [
            'fb' => FeedbackBimbingan::count(),
            'ks' => DataPsikolog::count(),
        ];

        return response()->json($data, 200);
    }
}
