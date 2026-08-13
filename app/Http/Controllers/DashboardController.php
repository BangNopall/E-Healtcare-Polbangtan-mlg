<?php

namespace App\Http\Controllers;

use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\User;

use App\Models\PresensiBimbingan;

class DashboardController extends Controller
{


    public function konseling()
    {
        $user = User::all();    
        $feedback = FeedbackBimbingan::all()->count();
        $konsultasi = DataPsikolog::all()->count();
        $jadwal = JadwalBimbingan::all();
        $presensi = PresensiBimbingan::all();

        $mahasiswa = $user->where('role', 'Mahasiswa')->count();
        $psikiater = $user->where('role', 'Psikiater')->count();

        // ambil jadwal bimbingan yang tanggalnya hari ini       
        $materitoday = $jadwal->where('tanggal', now()->format('Y-m-d'))->first();

        // ambil jadwal bimibingan 3 hari terakhir
        $lastjadwal = $jadwal->where('tanggal', '>=', now()->subDays(5)->format('Y-m-d'))
            ->where('tanggal', '<=', now()->format('Y-m-d'))
            ->sortByDesc('tanggal')
            ->take(5);

        // presensi senso hari ini
        $sakit = $presensi->where('status', 'Sakit')->where('tanggal_presensi', now()->format('Y-m-d'))->count();
        $izin = $presensi->where('status', 'Izin')->where('tanggal_presensi', now()->format('Y-m-d'))->count();
        $alpha = $presensi->where('status', 'Alpha')->where('tanggal_presensi', now()->format('Y-m-d'))->count();

        return view('konseling.dashboard', compact('mahasiswa', 'psikiater', 'feedback', 'konsultasi', 'jadwal', 'materitoday', 'lastjadwal', 'sakit', 'izin', 'alpha'));
    }
}
