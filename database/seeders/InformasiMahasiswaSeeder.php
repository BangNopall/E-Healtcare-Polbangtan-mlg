<?php

namespace Database\Seeders;

use App\Models\Blok;
use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InformasiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Prodi

        Prodi::create([
            'name' => 'PPB',
        ]);
        Prodi::create([
            'name' => 'PPKH',
        ]);
        Prodi::create([
            'name' => 'AGRINAK',
        ]);

        // Create Blok Ruangan
        Blok::create([
            'name' => 'A',
        ]);
        Blok::create([
            'name' => 'B',
        ]);
        Blok::create([
            'name' => 'C',
        ]);
        Blok::create([
            'name' => 'D',
        ]);
        Blok::create([
            'name' => 'E',
        ]);
    }
}
