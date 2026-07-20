<?php

namespace Database\Factories;

use App\Models\SatuanObat;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryObat>
 */
class InventoryObatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstSatuan = SatuanObat::first()->id;
        $LastSatuan = SatuanObat::latest('id')->first()->id;
        return [
            'stok' => 0,
            'satuan_id' => $this->faker->numberBetween($firstSatuan, $LastSatuan),
        ];
    }

    /**
     * unique kode_obat field for example PP001, PP002, PC001, etc
     */

    public function uniqueKodeObat($namaObat): string
    {
        $prefix = 'PO' . strtoupper(substr($namaObat, 0, 1));
        $lastObat = DB::table('inventory_obats')
            ->where('kode_obat', 'like', $prefix . '%')
            ->orderBy('kode_obat', 'desc')
            ->first();

        if ($lastObat) {
            $lastId = intval(substr($lastObat->kode_obat, 3));
            $newId = str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = '001';
        }

        return $prefix . $newId;
    }
}
