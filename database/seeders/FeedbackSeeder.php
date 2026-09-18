<?php

namespace Database\Seeders;

use App\Models\JadwalBimbingan;
use App\Models\User;
use App\Models\FeedbackBimbingan;
use App\Models\BimbinganSenso;
use App\Models\PresensiBimbingan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate Jadwal Bimbingan for current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        for ($date = $startOfMonth; $date <= $endOfMonth; $date->addDay()) {
            JadwalBimbingan::create([
                'tanggal' => $date->format('Y-m-d'),
                'materi' => 'Materi bimbingan ' . $date->format('d-m-Y'),
            ]);
        }

        $dataMahasiswa = User::where('role', 'mahasiswa')->get();
        $dataSenso = $dataMahasiswa->where('senso', 1);
        $dataNonSenso = $dataMahasiswa->where('senso', 0);

        // Daftarkan mahasiswa no senso ke senso
        foreach ($dataNonSenso as $mahasiswa) {
            $senso = $dataSenso->random();
            BimbinganSenso::create([
                'siswa_id' => $mahasiswa->id,
                'senso_id' => $senso->id,
            ]);
        }

        // Ubah Absensi Senso Menjadi Hadir
        foreach ($dataSenso as $senso) {
            $presensi = PresensiBimbingan::where('senso_id', $senso->id)->get();
            foreach ($presensi as $prn) {
                $prn->update([
                    'status' => 'hadir',
                ]);
            }
        }

        // Get all Jadwal Bimbingan
        $jadwalBimbingan = JadwalBimbingan::all();

        // Create feedback for each jadwal bimbingan
        foreach ($jadwalBimbingan as $jadwal) {
            foreach ($dataNonSenso as $siswa) {
                $senso = BimbinganSenso::where('siswa_id', $siswa->id)->first()->senso;

                FeedbackBimbingan::create([
                    'jadwal_id' => $jadwal->id,
                    'senso_id' => $senso->id,
                    'siswa_id' => $siswa->id,
                    'feedback' => 'Feedback for ' . $jadwal->materi . ' by ' . $siswa->name,
                ]);
            }
        }
    }
}
