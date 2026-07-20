<?php

namespace Database\Seeders;

use App\Models\KategoriConsumable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriConsumableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            'Peralatan Medis',
            'Peralatan Laboratorium',
            'Peralatan Tindakan Medis',
            'Peralatan Terapi',
            'Peralatan Diagnostik',
            'Peralatan Rehabilitasi',
            'Peralatan Kesehatan Lainnya',
        ];

        foreach ($kategoris as $kategori) {
            KategoriConsumable::factory()->create([
                'nama_kategori' => $kategori,
            ]);
        }
    }
}
