<?php

namespace Database\Seeders;

use App\Models\CDMI;
use App\Models\DMTI;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Asrama Polbangtan',
            'email' => 'admin@asramapolbangtan-mlg.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
        ]);
        User::create([
            'name' => 'Developer Asrama Polbangtan',
            'email' => 'developer@asramapolbangtan-mlg.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
        ]);
        // User::create([
        //     'name' => 'Admin Asrama Polbangtan',
        //     'email' => 'admin@gmail.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'Admin',
        // ]);
        // User::create([
        //     'name' => 'Developer Asrama Polbangtan',
        //     'email' => 'developer@gmail.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'Admin',
        // ]);


        User::create([
            'name' => 'Doctor Johny Sins',
            'email' => 'psikolog@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'Psikolog',
        ]);
        // User::create([
        //     'name' => 'Tegar Dito Priandika',
        //     'email' => 'tegar@gmail.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'Admin',
        // ]);
        User::create([
            'name' => 'nopal',
            'email' => 'nopal@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
        ]);

        // DB::transaction(function () {
        //     // Create a user
        //     $user = User::create([
        //         'name' => 'Agus setiawan',
        //         'email' => 'mahasiswa@gmail.com',
        //         'password' => bcrypt('password'),
        //         'role' => 'Mahasiswa',
        //         'cdmi' => 1,
        //         'cdmi_complete' => 0,
        //         'dmti' => 1,
        //         'dmti_complete' => 0,
        //     ]);

        //     // Create DMTI for the user
        //     DMTI::create([
        //         'user_id' => $user->id,
        //         'nik' => '121212121212',
        //         'no_bpjs' => '1234567890',
        //         'no_hp' => $this->generateUniquePhoneNumber(),
        //         'tempat_kelahiran' => 'Bandung',
        //         'tanggal_lahir' => '2000-01-01',
        //         'jenis_kelamin' => 'pria',
        //         'golongan_darah' => 'AB+',
        //         'usia' => 21,
        //     ]);

        //     // Create CDMI for the user
        //     CDMI::create([
        //         'user_id' => $user->id,
        //         'nim' => '1234567890',
        //         'no_ruangan' => '10',
        //         'prodi_id' => 2,
        //         'blok_id' => 3,
        //     ]);

        //     // Update the user to mark CDMI and DMTI as complete
        //     $user->update([
        //         'cdmi_complete' => 1,
        //         'dmti_complete' => 1,
        //         'nim' => '1234567890',
        //     ]);
        // });

        // User::factory()->create([
        //     'name' => 'Budi Santoso',
        //     'email' => 'mahasiswa1@gmail.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'Mahasiswa',
        //     'cdmi' => 1,
        //     'cdmi_complete' => 0,
        //     'dmti' => 1,
        //     'dmti_complete' => 0,
        // ]);

        // DB::transaction(function () {
        //     // Create a user
        //     $user = User::factory()->create([
        //         'name' => 'Karyawan Polbangtan Malang',
        //         'email' => 'karyawan@gmail.com',
        //         'password' => bcrypt('password'),
        //         'role' => 'Karyawan',
        //         'dmti' => 1,
        //         'dmti_complete' => 0,
        //     ]);

        //     // Create DMTI for the user
        //     DMTI::create([
        //         'user_id' => $user->id,
        //         'nik' => '121212121213',
        //         'no_bpjs' => '1234567891',
        //         'no_hp' => $this->generateUniquePhoneNumber(),
        //         'tempat_kelahiran' => 'Bandung',
        //         'tanggal_lahir' => '2000-01-01',
        //         'jenis_kelamin' => 'pria',
        //         'golongan_darah' => 'AB+',
        //         'usia' => 21,
        //     ]);

        //     // Update the user to mark DMTI as complete
        //     $user->update([
        //         'dmti_complete' => 1,
        //     ]);
        // });

        // User::create([
        //     'name' => 'Heri Kurniawan',
        //     'email' => 'karyawan1@gmail.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'Karyawan',
        //     'dmti' => 1,
        //     'dmti_complete' => 0,
        // ]);

        // User::factory(10)->create([
        //     'role' => 'Mahasiswa',
        //     'senso' => 1,
        // ]);
        // User::factory(150)->create([
        //     'role' => 'Mahasiswa',
        // ]);

        // get 50 mahasiswa yang belum cdmi_complete dan buatkan cdmi
        // $mahasiswa = User::where('role', 'Mahasiswa')->where('cdmi_complete', 0)->orderBy('name', 'asc')->limit(50)->get();
        // foreach ($mahasiswa as $mhs) {
        //     DB::transaction(function () use ($mhs) {
        //         // Generate unique NIM
        //         $nim = $this->generateUniqueNim();

        //         // Create CDMI for the user
        //         CDMI::create([
        //             'user_id' => $mhs->id,
        //             'nim' => $nim,
        //             'no_ruangan' => rand(1, 12),
        //             'prodi_id' => rand(1, 3),
        //             'blok_id' => rand(1, 5),
        //         ]);

        //         // Update the user to mark CDMI as complete
        //         $mhs->update([
        //             'cdmi_complete' => 1,
        //             'nim' => $nim,
        //         ]);
        //     });
        // }

        // // get 50 mahasiswa yang belum dmti_complete dan buatkan dmti
        // $mahasiswa = User::where('role', 'Mahasiswa')->where('dmti_complete', 0)->orderBy('name', 'asc')->limit(50)->get();
        // foreach ($mahasiswa as $mhs) {
        //     DB::transaction(function () use ($mhs) {
        //         // Create DMTI for the user
        //         DMTI::create([
        //             'user_id' => $mhs->id,
        //             'nik' => $this->generateUniqueNik(),
        //             'no_bpjs' => rand(1000000000, 9999999999),
        //             'no_hp' => $this->generateUniquePhoneNumber(),
        //             'tempat_kelahiran' => 'Bandung',
        //             'tanggal_lahir' => '2000-01-01',
        //             'jenis_kelamin' => 'pria',
        //             'golongan_darah' => 'AB+',
        //             'usia' => 21,
        //         ]);

        //         // Update the user to mark DMTI as complete
        //         $mhs->update([
        //             'dmti_complete' => 1,
        //         ]);
        //     });
        // }
    }

    // private function generateUniqueNim()
    // {
    //     do {
    //         $nim = rand(1000000000, 9999999999);
    //     } while (CDMI::where('nim', $nim)->exists());

    //     return $nim;
    // }

    // private function generateUniqueNik()
    // {
    //     do {
    //         $nik = rand(100000000000, 999999999999);
    //     } while (DMTI::where('nik', $nik)->exists());

    //     return $nik;
    // }

    // private function generateUniquePhoneNumber()
    // {
    //     do {
    //         $no_hp = '08' . rand(1000000000, 9999999999);
    //     } while (DMTI::where('no_hp', $no_hp)->exists());

    //     return $no_hp;
    // }
}
