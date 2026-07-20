<?php

namespace Database\Seeders;

use App\Models\KategoriAlat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriAlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            'Alat Kesehatan',
            'Alat Laboratorium',
            'Alat Tindakan Medis',
            'Alat Terapi',
            'Alat Diagnostik',
            'Alat Rehabilitasi',
            'Alat Kesehatan Lainnya',
        ];

        foreach ($kategoris as $kategori) {
            KategoriAlat::factory()->create([
                'nama_kategori' => $kategori,
            ]);
        }
    }
}
