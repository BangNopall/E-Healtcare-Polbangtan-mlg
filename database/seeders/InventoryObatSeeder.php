<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ObatLog;
use App\Models\InventoryLog;
use App\Models\InventoryObat;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InventoryObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $namaObat = [
            'Paracetamol', 'Amoxilin', 'Cetirizine', 'Betadine', 'Promag',
            'Bodrex', 'Antangin', 'Decolgen', 'OBH', 'Antalgin', 'Aspirin',
            'Ibuprofen', 'Naproxen', 'Diclofenac', 'Celecoxib', 'Mefenamic',
            'Indomethacin', 'Etoricoxib', 'Piroxicam', 'Meloxicam', 'Lornoxicam',
            'Ketoprofen', 'Phenylbutazone', 'Rofecoxib', 'Oxaprozin', 'Sulindac',
            'Etodolac', 'Ketorolac', 'Nabumetone', 'Meclofenamate', 'Piroxicam',
            'Diflunisal', 'Salsalate', 'Choline salicylate', 'Magnesium salicylate',
            'Sodium salicylate', 'Acetaminophen', 'Allopurinol', 'Amlodipine',
            'Atorvastatin', 'Azithromycin', 'Baclofen', 'Bisoprolol', 'Carvedilol',
            'Cephalexin', 'Ciprofloxacin', 'Clindamycin', 'Clopidogrel', 'Diazepam',
            'Diclofenac', 'Digoxin', 'Diltiazem', 'Doxycycline', 'Enalapril',
            'Esomeprazole', 'Furosemide', 'Gabapentin', 'Glyceryl Trinitrate',
            'Hydrochlorothiazide', 'Ibuprofen', 'Losartan', 'Metformin', 'Metoprolol',
            'Nifedipine', 'Omeprazole', 'Phenoxymethylpenicillin',
            'Prednisolone', 'Propranolol', 'Simvastatin', 'Tamsulosin', 'Tramadol',
            'Warfarin', 'Xylometazoline'
        ];

        foreach ($namaObat as $obat) {
            // create obat
            $firstDay = Carbon::now()->startOfMonth()->day;
            $lastDay = Carbon::now()->endOfMonth()->day;
            $this->CreateObat($obat, $firstDay, $lastDay);

            $getIdObatFromInventory = InventoryObat::where('nama_obat', $obat)->first()->id;

            // deposit obat
            $randomDepositLooping = rand(1, 10);
            for ($i = 0; $i < $randomDepositLooping; $i++) {
                $this->DepositObat($obat, $getIdObatFromInventory);
            }

            // withdraw obat / mahasiswa
            $this->WithdrawObat($obat, $getIdObatFromInventory);
        }
    }

    private function CreateObat($obat, $firstDay, $lastDay)
    {
        $randomDay = rand($firstDay, $lastDay);
        $getDay = Carbon::now()->subDays($randomDay);
        $idDokter = User::where('role', 'Dokter')->get()->random()->id;
        InventoryObat::factory()->create([
            'nama_obat' => $obat,
            'kode_obat' => InventoryObat::factory()->uniqueKodeObat($obat),
            'createdBy' => $idDokter,
        ]);
        InventoryLog::create([
            'obat_id' => InventoryObat::where('nama_obat', $obat)->first()->id,
            'type' => 'created',
            'Qty' => 0,
            'description' => 'Created'. $obat .' By Seeder',
            'user_id' => $idDokter,
        ]);
    }
    private function DepositObat($obat, $getIdObatFromInventory)
    {
        $idDokter = User::where('role', 'Dokter')->get()->random()->id;
        $randomAddDays = rand(1, 7);
        $randomDeposit = rand(10, 1000);
        InventoryObat::where('nama_obat', $obat)->first()->update([
            'stok' => $randomDeposit,
        ]);
        InventoryLog::create([
            'obat_id' => $getIdObatFromInventory,
            'type' => 'deposit',
            'Qty' => $randomDeposit,
            'production_date' => now()->subDays($randomAddDays),
            'expired_date' => now()->addMonths($randomAddDays),
            'description' => 'Created By Seeder',
            'user_id' => $idDokter,
        ]);
    }
    private function WithdrawObat($obat, $getIdObatFromInventory)
    {
        $idMahasiswa = User::where('role', 'Mahasiswa')->get()->random()->id;
        $dataObat = InventoryObat::where('nama_obat', $obat)->first();
        $stokObat = $dataObat->stok;
        while ($stokObat > 0) {
            $randomWithdraw = rand(1, $stokObat);
            $stokObat -= $randomWithdraw;

            InventoryObat::where('nama_obat', $obat)->update([
                'stok' => $stokObat,
            ]);

            ObatLog::create([
                'obat_id' => $getIdObatFromInventory,
                'Qty' => $randomWithdraw,
                'type' => 'withdraw',
                'production_date' => now()->subDays($randomWithdraw),
                'expired_date' => now()->addMonths($randomWithdraw),
                'user_id' => $idMahasiswa,
            ]);
        }
    }
}
