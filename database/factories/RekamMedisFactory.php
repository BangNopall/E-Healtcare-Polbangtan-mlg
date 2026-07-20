<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\InventoryAlat;
use App\Models\InventoryObat;
use InvalidArgumentException;
use App\Models\InventoryConsumable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RekamMedis>
 */
class RekamMedisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pengecekan untuk memastikan data user dengan role 'Dokter' tersedia
        $dokter = User::where('role', 'Dokter')->inRandomOrder()->first();
        if (!$dokter) {
            throw new InvalidArgumentException("No user with role 'Dokter' found.");
        }

        // Pengecekan untuk memastikan data user dengan role 'Mahasiswa' atau 'Karyawan' tersedia
        $pasien = User::where('role', 'Mahasiswa')->orWhere('role', 'Karyawan')->inRandomOrder()->first();
        if (!$pasien) {
            throw new InvalidArgumentException("No user with role 'Mahasiswa' or 'Karyawan' found.");
        }

        // Mendapatkan sejumlah obat secara acak dari InventoryObat
        $jumlahObatYangDiambil = rand(1, 5); // Sesuaikan dengan jumlah obat yang ingin diambil
        $obatList = InventoryObat::inRandomOrder()->take($jumlahObatYangDiambil)->get();

        // Mendapatkan sejumlah alat dari InventoryAlat dan InventoryConsumable
        $jumlahAlatYangDiambil = rand(1, 5); // Sesuaikan dengan jumlah alat yang ingin diambil
        $alatList = InventoryAlat::inRandomOrder()->take($jumlahAlatYangDiambil)->get();
        $consumableList = InventoryConsumable::inRandomOrder()->take($jumlahAlatYangDiambil)->get();

        $data1 = $alatList->map(function ($item) {
            $item['identity_alat'] = 'alat';
            return $item->only(['id', 'nama_alat', 'identity_alat']);
        })->toArray();

        $data2 = $consumableList->map(function ($item) {
            $item['identity_alat'] = 'consumable';
            return $item->only(['id', 'nama_alat', 'identity_alat']);
        })->toArray();

        // Menggabungkan data alat dari kedua tabel
        $dataAlat = array_merge($data1, $data2);

        if ($obatList->isEmpty()) {
            throw new InvalidArgumentException("No obat found in inventory.");
        }

        // Menginisialisasi array tindakan
        $tindakan = [
            'obat_id' => [],
            'nama_obat' => [],
            'jumlah_obat' => [],
            'alat_id' => [],
            'nama_alat' => [],
            'jumlah_alat' => [],
            'identity_alat' => [],
        ];

        // Mengisi tindakan dengan data obat yang diambil
        foreach ($obatList as $obat) {
            $jumlahObat = rand(1, 10);

            $tindakan['obat_id'][] = $obat->id;
            $tindakan['nama_obat'][] = $obat->nama_obat;
            $tindakan['jumlah_obat'][] = $jumlahObat;

            // Mengurangi stok obat yang sudah dipakai
            $InventoryObat = InventoryObat::find($obat->id);
            $InventoryObat->decrement('stok', $jumlahObat);
        }

        // Mengisi tindakan dengan data alat yang diambil
        foreach ($dataAlat as $alat) {
            $tindakan['alat_id'][] = $alat['id'];
            $tindakan['nama_alat'][] = $alat['nama_alat'];
            $tindakan['identity_alat'][] = $alat['identity_alat'];
            $tindakan['jumlah_alat'][] = 1; // Misalnya, setiap alat diambil satu buah saja

            // Mengurangi stok alat yang sudah dipakai
            if ($alat['identity_alat'] === 'alat') {
                $InventoryAlat = InventoryAlat::find($alat['id']);
            } else {
                $InventoryAlat = InventoryConsumable::find($alat['id']);
            }
            $InventoryAlat->decrement('stok', 1); // Misalnya, pengurangan stok alat satu buah saja
        }

        return [
            'tanggal' => $this->faker->date(),
            'dokter_id' => $dokter->id,
            'pasien_id' => $pasien->id,
            'keluhan' => $this->faker->text(),
            'pemeriksaan' => $this->faker->text(),
            'diagnosa' => $this->faker->text(),
            'tindakan' => json_encode($tindakan),
            'withObat' => true,
            'withAlat' => true,
            'rawatjalan' => $this->faker->text(),
            'rs_name_rujukan' => $this->faker->text(),
            'rs_name_rawatinap' => $this->faker->text(),
        ];
    }
}
