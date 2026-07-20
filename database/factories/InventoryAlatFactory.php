<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryAlat>
 */
class InventoryAlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstKategori = DB::table('kategori_alats')->first()->id;
        $lastKategori = DB::table('kategori_alats')->latest('id')->first()->id;
        return [
            'stok' => 0,
            'kategori_id' => $this->faker->numberBetween($firstKategori, $lastKategori),
        ];
    }

    public function uniqueKodeAlat($namaAlat): string
    {
        $prefix = 'PA' . strtoupper(substr($namaAlat, 0, 1));
        $lastAlat = DB::table('inventory_alats')
            ->where('kode_alat', 'like', $prefix . '%')
            ->orderBy('kode_alat', 'desc')
            ->first();

        if ($lastAlat) {
            $lastId = intval(substr($lastAlat->kode_alat, 3));
            $newId = str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = '001';
        }

        return $prefix . $newId;
    }
}
