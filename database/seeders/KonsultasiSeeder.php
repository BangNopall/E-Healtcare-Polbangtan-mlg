<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class KonsultasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // get Mahasiswa cdmi_complete = 1
        $mahasiswa = User::where('cdmi_complete', 1)->get();

        // buat dataPsikolog
        foreach ($mahasiswa as $mhs) {
            $mhs->dataPsikolog()->create([
                'tanggal' => $faker->dateTimeThisYear(),
                'keluhan' => $faker->sentence(),
                'metode_psikologi' => $faker->randomElement(['Psikoanalisis', 'Kognitif', 'Behavioral', 'Humanistik']),
                'diagnosa' => $faker->randomElement(['Depresi', 'Kecemasan', 'Stress', 'Gangguan Bipolar']),
                'prognosis' => $faker->randomElement(['Baik', 'Sedang', 'Buruk']),
                'intervensi' => $faker->randomElement(['Konseling', 'Terapi Obat', 'Psikoterapi']),
                'saran' => $faker->sentence(),
                'rencana_tindak_lanjut' => $faker->randomElement(['Konseling rutin', 'Terapi mingguan', 'Pemeriksaan lanjutan']),
            ]);
        }
    }
}
