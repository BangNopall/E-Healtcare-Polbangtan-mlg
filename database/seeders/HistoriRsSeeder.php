<?php

namespace Database\Seeders;

use App\Models\HistoriRs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HistoriRsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'RSUD. Dr. Saiful Anwar',
            'RS. Tk.II dr. Soepraoen',
            'RSIA Melati Children Hospital',
            'RUMAH SAKIT IBU DAN ANAK HERMINA MALANG',
            'Muhammadiyah University Malang Hospital',
            'RS Brimedika Malang',
            'RS Dr. Soepraoen',
            'RS Hermina Tangkubanprahu',
            'RS Islam Aisyiyah Malang',
            'RS Islam Malang',
            'RS Lavalette Malang',
            'RS Panti Nirmala',
            'RS Panti Waluya Sawahan',
            'RS Permata Bunda',
            'RS Persada Malang',
            'RS Universitas Brawijaya',
            'RSIA Galeri Candra',
            'RSIA Mardi Waloeja',
            'RSIA Mardi Waloeja Rampal',
            'RSIA Melati Husada',
            'RSIA Muhammadiyah Malang',
            'RSIA Mutiara Bunda Malang',
            'RSIA Permata Hati',
            'RSIA Puri Bunda',
            'RSIA Puri Malang',
            'RSIA Refa Husada',
            'RSIA Rumkitbab Malang'
        ];

        foreach ($data as $nama_rs) {
            HistoriRs::create([
                'nama_rs' => $nama_rs
            ]);
        }
    }
}
