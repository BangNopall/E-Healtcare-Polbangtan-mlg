<?php

namespace Database\Seeders;

use App\Models\ConsumableLog;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\InventoryConsumable;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InventoryConsumableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consumables = [
            'aqua galon', 'aqua botol', 'aqua gelas', 'aqua cup', 'beras', 'gula', 'minyak',
            'sabun', 'shampoo', 'sikat gigi', 'pasta gigi', 'tissue', 'masker', 'sarung tangan',
            'kantong sampah', 'kantong plastik', 'kantong kresek', 'kantong kertas', 'kantong kardus',
            'kantong kain', 'kantong kanvas', 'kantong karet', 'kantong kulit', 'kantong kaca',
            'kantong kayu', 'kantong besi', 'kantong baja', 'kantong aluminium', 'kantong plastik',
        ];

        foreach ($consumables as $consumable) {
            $firstDay = Carbon::now()->startOfMonth()->day;
            $lastDay = Carbon::now()->endOfMonth()->day;
            $this->CreateConsumable($consumable, $firstDay, $lastDay);

            $getConsumableFromInventory = InventoryConsumable::where('nama_alat', $consumable)->first()->id;

            $randomDepositLooping = rand(1, 10);
            for ($i = 0; $i < $randomDepositLooping; $i++) {
                $this->DepositConsumable($consumable, $getConsumableFromInventory);
            }

            $this->WithdrawConsumable($consumable, $getConsumableFromInventory);
        }
    }

    public function CreateConsumable($consumable, $firstDay, $lastDay)
    {
        $randomDay = rand($firstDay, $lastDay);
        $getDay = Carbon::now()->subDays($randomDay);
        $adminUsers = User::where('role', 'Admin')->get();
        if ($adminUsers->isEmpty()) {
            throw new \Exception('No Admin users found.');
        }
        $idAdmin = $adminUsers->random()->id;

        InventoryConsumable::factory()->create([
            'nama_alat' => $consumable,
            'kode_alat' => InventoryConsumable::factory()->uniqueKodeConsumable($consumable),
            'createdBy' => $idAdmin,
        ]);
        ConsumableLog::create([
            'alat_id' => InventoryConsumable::where('nama_alat', $consumable)->first()->id,
            'Qty' => 0,
            'description' => 'Create ' . $consumable . ' By Seeder',
            'user_id' => $idAdmin,
            'date' => $getDay,
            'time' => $getDay,
        ]);
    }

    public function DepositConsumable($consumable, $getConsumableFromInventory)
    {
        $adminUsers = User::where('role', 'Admin')->get();
        if ($adminUsers->isEmpty()) {
            throw new \Exception('No Admin users found.');
        }
        $idAdmin = $adminUsers->random()->id;

        $randomAddDays = rand(1, 7);
        $randomDeposit = rand(10, 500);
        InventoryConsumable::where('nama_alat', $consumable)->first()->update([
            'stok' => $randomDeposit,
        ]);
        $randDate = new DateTime();
        $randDate->setTime(mt_rand(0, 23), mt_rand(0, 59));
        ConsumableLog::create([
            'alat_id' => $getConsumableFromInventory,
            'type' => 'deposit',
            'Qty' => $randomDeposit,
            'description' => 'Create By Seeder',
            'user_id' => $idAdmin,
            'date' => now()->subDays($randomAddDays),
            'time' => $randDate->format('H:i:s'),
        ]);
    }

    public function WithdrawConsumable($consumable, $getConsumableFromInventory)
    {
        $dokterUsers = User::where('role', 'Dokter')->get();
        if ($dokterUsers->isEmpty()) {
            throw new \Exception('No Dokter users found.');
        }
        $idDokter = $dokterUsers->random()->id;

        $dataAlat = InventoryConsumable::where('nama_alat', $consumable)->first();
        $stokAlat = $dataAlat->stok;
        while($stokAlat > 0) {
            $randomWithdraw = rand(1, min($stokAlat, 10));
            $stokAlat -= $randomWithdraw;

            InventoryConsumable::where('nama_alat', $consumable)->first()->update([
                'stok' => $stokAlat,
            ]);

            $randDate = new DateTime();
            $randDate->setTime(mt_rand(0, 23), mt_rand(0, 59));
            ConsumableLog::create([
                'alat_id' => $getConsumableFromInventory,
                'type' => 'withdraw',
                'Qty' => $randomWithdraw,
                'description' => 'WD By Seeder',
                'user_id' => $idDokter,
                'date' => now(),
                'time' => $randDate->format('H:i:s'),
            ]);
        }
    }
}
