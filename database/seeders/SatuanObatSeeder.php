<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SatuanObat;

class SatuanObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataSatuanObat = [
            'Botol 30 ml',
            'Botol 60 ml',
            'Tablet',
            'Kapsul',
            'Sirup',
            'Ampul',
            'Strip',
            'Oles',
            'Oles',
            'Suntikan',
            'semprotan',
            'Patch',
            'Puyer',
            'Kotak',
            'Tube',
            'Pcs',
            'Vial',
        ];

        foreach ($dataSatuanObat as $satuan) {
            SatuanObat::factory()->create([
                'nama_satuan' => $satuan,
            ]);
        }
    }
}
