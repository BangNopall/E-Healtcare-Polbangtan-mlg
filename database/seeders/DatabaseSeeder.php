<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\SatuanObatSeeder;
use Database\Seeders\KategoriAlatSeeder;
use Database\Seeders\InventoryAlatSeeder;
use Database\Seeders\InventoryObatSeeder;
use Database\Seeders\KategoriConsumableSeeder;
use Database\Seeders\InventoryConsumableSeeder;

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
        // $this->call(MahasiswaSeeder::class);

        // Histori RS Seeder Dummy Data
        $this->call(HistoriRsSeeder::class);

        // Obat Seeder Dummy Data
        $this->call(SatuanObatSeeder::class);
        $this->call(InventoryObatSeeder::class);

        // Alat Seeder Dummy Data
        $this->call(KategoriAlatSeeder::class);
        $this->call(InventoryAlatSeeder::class);

        // Consumable Seeder Dummy Data
        $this->call(KategoriConsumableSeeder::class);
        $this->call(InventoryConsumableSeeder::class);

        // Surat Medical dummy data
        $this->call(SuratMedicalSeeder::class);

        // Feeback dummy data
        $this->call(FeedbackSeeder::class);

        // konsultasi dummy data
        $this->call(KonsultasiSeeder::class);
    }
}
