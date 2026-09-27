<?php

namespace App\Http\Controllers;

use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\User;

use App\Models\PresensiBimbingan;
use App\Models\DataPsikolog;
class DashboardController extends Controller
{


    public function konseling()
    {
        $feedback = FeedbackBimbingan::count();
        $konsultasi = DataPsikolog::count();

        $mahasiswa = User::where('role', 'Mahasiswa')->count();
        $psikolog = User::where('role', 'Psikolog')->count();

        // ambil jadwal bimbingan yang tanggalnya hari ini       
        $today = now()->format('Y-m-d');
        $materitoday = JadwalBimbingan::where('tanggal', $today)->first();

        // ambil jadwal bimbingan 5 hari terakhir
        $lastjadwal = JadwalBimbingan::where('tanggal', '>=', now()->subDays(5)->format('Y-m-d'))
            ->where('tanggal', '<=', $today)
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // presensi senso hari ini
        $sakit = PresensiBimbingan::where('status', 'Sakit')->where('tanggal_presensi', $today)->count();
        $izin = PresensiBimbingan::where('status', 'Izin')->where('tanggal_presensi', $today)->count();
        $alpha = PresensiBimbingan::where('status', 'Alpha')->where('tanggal_presensi', $today)->count();

        $jadwal = $lastjadwal;

        return view('konseling.dashboard', compact('mahasiswa', 'psikolog', 'feedback', 'konsultasi', 'jadwal', 'materitoday', 'lastjadwal', 'sakit', 'izin', 'alpha'));
    }
}
