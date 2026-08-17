<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Informasi Mahasiswa Seeder Dummy Data
        $this->call(InformasiMahasiswaSeeder::class);

        // User Seeder Dummy Data
        $this->call(UserSeeder::class);
        
        // Mahasiswa Seeder 
        $this->call(MahasiswaSeeder::class);



        // Feeback dummy data
        $this->call(FeedbackSeeder::class);

        // konsultasi dummy data
        $this->call(KonsultasiSeeder::class);
    }
}
