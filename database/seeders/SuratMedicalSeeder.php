<?php

namespace Database\Seeders;

use App\Models\HistoriRs;
use App\Models\RekamMedis;
use App\Models\SuratKeteranganBerobat;
use App\Models\SuratKeteranganSakit;
use App\Models\SuratRujukan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SuratMedicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $suratBerobat = 16;
        $suratSakit = 9;
        $suratRujukan = 21;

        // mahasiswa
        $mahasiswa = User::where('role', 'mahasiswa')->with('getCDMI', 'getDMTI')->get();

        // Dokter
        $dokter = User::where('role', 'dokter')->get();

        // get daftar RS
        $rumahSakit = HistoriRs::all();

        // get CDMI and DMTI complete
        $mahasiswaDataLengkap = $mahasiswa->where('cdmi_complete', 1)->where('dmti_complete', 1);

        // create surat keterangan obat
        $mahasiswaObat = $mahasiswaDataLengkap->random($suratBerobat);
        foreach ($mahasiswaObat as $mahasiswa) {
            $dokterPerawat = $dokter->random();

            $tanggalLahirFormatted = Carbon::parse($mahasiswa->getDMTI->tanggal_lahir)->translatedFormat('d-m-Y');
            $tempatKelahiran = $mahasiswa->getDMTI->tempat_kelahiran;
            SuratKeteranganBerobat::create([
                'dokter_id' => $dokterPerawat->id,
                'pasien_id' => $mahasiswa->id,
                'nama_dokter' => $dokterPerawat->name,
                'jabatan_dokter' => 'Dokter',
                'nama_pasien' => $mahasiswa->name,
                'jabatan_pasien' => 'Mahasiswa',
                'keluhan' => $faker->randomElement(['Psikoanalisis', 'Kognitif', 'Behavioral', 'Humanistik']),
                'pemeriksaan' => $faker->sentence(),
                'diagnosa' => $faker->randomElement(['Depresi', 'Kecemasan', 'Stress', 'Gangguan Bipolar']),
                'withObat' => false,
                'nim' => $mahasiswa->getCDMI->nim,
                'nik' => $mahasiswa->getDMTI->nik,
                'no_bpjs' => $mahasiswa->getDMTI->no_bpjs,
                'no_hp' => $mahasiswa->getDMTI->no_hp,
                'ttl' => "$tempatKelahiran, $tanggalLahirFormatted",
                'jenis_kelamin' => $mahasiswa->getDMTI->jenis_kelamin,
                'usia' => $mahasiswa->getDMTI->usia,
                'golongan_darah' => $mahasiswa->getDMTI->golongan_darah,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // create surat keterangan sakit
        $mahasiswaSakit = $mahasiswaDataLengkap->random($suratSakit);
        foreach ($mahasiswaSakit as $mahasiswa) {
            $dokterPerawat = $dokter->random();

            $tanggalLahirFormatted = Carbon::parse($mahasiswa->getDMTI->tanggal_lahir)->translatedFormat('d-m-Y');
            $tempatKelahiran = $mahasiswa->getDMTI->tempat_kelahiran;


            $tanggalMulai = now()->subDays($faker->numberBetween(1, 10));
            $tanggalAkhir = now()->addDays($faker->numberBetween(1, 10));
            $lamaSakit = $tanggalMulai->diffInDays($tanggalAkhir);

            SuratKeteranganSakit::create([
                'dokter_id' => $dokterPerawat->id,
                'pasien_id' => $mahasiswa->id,
                'nama_dokter' => $dokterPerawat->name,
                'jabatan_dokter' => 'Dokter',
                'nama_pasien' => $mahasiswa->name,
                'jabatan_pasien' => 'Mahasiswa',

                'nim' => $mahasiswa->getCDMI->nim,
                'no_ruangan' => $mahasiswa->getCDMI->no_ruangan,
                'prodi_id' => $mahasiswa->getCDMI->prodi_id,
                'blok_id' => $mahasiswa->getCDMI->blok_id,

                'nik' => $mahasiswa->getDMTI->nik,
                'no_hp' => $mahasiswa->getDMTI->no_hp,
                'ttl' => "$tempatKelahiran, $tanggalLahirFormatted",
                'jenis_kelamin' => $mahasiswa->getDMTI->jenis_kelamin,
                'usia' => $mahasiswa->getDMTI->usia,

                'tanggal_mulai' => $tanggalMulai,
                'tanggal_akhir' => $tanggalAkhir,
                'lama_sakit' => $lamaSakit,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // create surat rujukan
        $mahasiswaRujukan = $mahasiswaDataLengkap->random($suratRujukan);
        foreach ($mahasiswaRujukan as $mahasiswa) {
            $dokterPerawat = $dokter->random();

            $tanggalLahirFormatted = Carbon::parse($mahasiswa->getDMTI->tanggal_lahir)->translatedFormat('d-m-Y');
            $tempatKelahiran = $mahasiswa->getDMTI->tempat_kelahiran;

            $rumahSakitRandom = $rumahSakit->random();

            SuratRujukan::create([
                'dokter_id' => $dokterPerawat->id,
                'pasien_id' => $mahasiswa->id,
                'nama_dokter' => $dokterPerawat->name,
                'jabatan_dokter' => 'Dokter',
                'nama_pasien' => $mahasiswa->name,
                'jabatan_pasien' => 'Mahasiswa',
                'nama_rs' => $rumahSakitRandom->nama_rs,

                'nik' => $mahasiswa->getDMTI->nik,
                'no_hp' => $mahasiswa->getDMTI->no_hp,
                'ttl' => "$tempatKelahiran, $tanggalLahirFormatted",
                'jenis_kelamin' => $mahasiswa->getDMTI->jenis_kelamin,
                'usia' => $mahasiswa->getDMTI->usia,

                'nim' => $mahasiswa->getCDMI->nim,
                'no_ruangan' => $mahasiswa->getCDMI->no_ruangan,
                'prodi_id' => $mahasiswa->getCDMI->prodi_id,
                'blok_id' => $mahasiswa->getCDMI->blok_id,

                'keluhan' => $faker->sentence(),
                'diagnosa' => $faker->sentence(),
                'kasus' => $faker->sentence(),
                'tindakan' => $faker->sentence(),
            ]);
        }

        // create Rekam Medis
        // RekamMedis::factory(142)->create();
    }

}
