<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryConsumable>
 */
class InventoryConsumableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstKategori = DB::table('kategori_consumables')->first()->id;
        $lastKategori = DB::table('kategori_consumables')->latest('id')->first()->id;
        return [
            'stok' => 0,
            'kategori_id' => $this->faker->numberBetween($firstKategori, $lastKategori),
        ];
    }

    public function uniqueKodeConsumable($namaConsumable): string
    {
        $prefix = 'PC' . strtoupper(substr($namaConsumable, 0, 1));
        $lastConsumable = DB::table('inventory_consumables')
            ->where('kode_alat', 'like', $prefix . '%')
            ->orderBy('kode_alat', 'desc')
            ->first();

        if ($lastConsumable) {
            $lastId = intval(substr($lastConsumable->kode_alat, 3));
            $newId = str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = '001';
        }

        return $prefix . $newId;
    }
}
