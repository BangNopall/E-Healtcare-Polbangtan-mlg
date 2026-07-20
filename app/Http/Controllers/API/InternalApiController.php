<?php

namespace App\Http\Controllers\API;

use DB;
use App\Models\User;
use App\Models\AlatLog;
use App\Models\HistoriRs;
use Nette\Utils\DateTime;
use App\Models\SatuanObat;
use App\Models\DataPsikolog;
use App\Models\InventoryLog;
use App\Models\KategoriAlat;
use App\Models\SuratRujukan;
use Illuminate\Http\Request;
use App\Models\ConsumableLog;
use App\Models\InventoryAlat;
use App\Models\InventoryObat;
use App\Models\BimbinganSenso;
use Illuminate\Support\Carbon;
use App\Models\FeedbackBimbingan;
use App\Models\KategoriConsumable;
use Illuminate\Routing\Controller;
use App\Models\InventoryConsumable;
use App\Models\SuratKeteranganSakit;
use App\Models\SuratKeteranganSehat;
use App\Models\SuratKeteranganBerobat;

class InternalApiController extends Controller
{
    public function getKategoriObat()
    {
        $data = SatuanObat::orderBy('nama_satuan', 'asc')->get()->toArray();
        return response()->json($data, 200);
    }

    public function getDaftarObat()
    {
        $data = InventoryObat::select('id', 'nama_obat', 'satuan_id', 'stok')->get()->toArray();
        return response()->json($data, 200);
    }

    public function getDaftarAlat()
    {
        // Ambil data dari InventoryAlat dan tambahkan identitas 'alat'
        $data1 = InventoryAlat::select('id', 'nama_alat')
            ->get()
            ->map(function ($item) {
                $item['identity'] = 'alat';
                return $item;
            })
            ->toArray();

        // Ambil data dari InventoryConsumable dan tambahkan identitas 'consumable'
        $data2 = InventoryConsumable::select('id', 'nama_alat')
            ->get()
            ->map(function ($item) {
                $item['identity'] = 'consumable';
                return $item;
            })
            ->toArray();

        // Gabungkan data1 dan data2
        $data = array_merge($data1, $data2);

        // Kembalikan data sebagai JSON response
        return response()->json($data, 200);
    }

    public function get_user()
    {
        $data = User::select('id', 'name')->whereNot('role', 'Admin')->orderBy('name', 'asc')->get()->toArray();
        return response()->json($data, 200);
    }
    public function getKategoriAlat()
    {
        $data = KategoriAlat::select('id', 'nama_kategori')->get()->toArray();
        // dd($data);
        return response()->json($data, 200);
    }

    public function getKategoriConsumables()
    {
        $data = KategoriConsumable::select('id', 'nama_kategori')->get()->toArray();
        return response()->json($data, 200);
    }

    public function daftarRS()
    {
        $data = HistoriRs::select('id', 'nama_rs')->get()->toArray();
        return response()->json($data, 200);
    }

    public function userNoSenso()
    {
        $data = User::where('senso', 0)->where('role', 'Mahasiswa')->orderBy('name', 'asc')->get()->toArray();
        return response()->json($data, 200);
    }

    public function userNoSensoNoAnakAsuh()
    {
        // Inisialisasi array data
        $data = [];

        // Ambil semua user dengan kondisi senso = 0 dan role = Mahasiswa
        $users = User::where('senso', 0)->where('role', 'Mahasiswa')->orderBy('name', 'asc')->get();

        // Iterasi setiap user
        foreach ($users as $user) {
            // Cek apakah user sudah terdaftar di BimbinganSenso
            $bimbinganSenso = BimbinganSenso::where('siswa_id', $user->id)->first();

            // Jika tidak ditemukan, tambahkan user ke dalam array data
            if (!$bimbinganSenso) {
                $data[] = $user;
            }
        }

        // Kembalikan response JSON dengan data user
        return response()->json($data, 200);
    }

    public function getDataObatBulan()
    {
        // Mengambil tanggal saat ini
        $today = now();

        // Menghitung tanggal 6 bulan yang lalu dari sekarang
        $sixMonthsAgo = clone $today;
        $sixMonthsAgo->subMonths(6);

        // Mengambil data stok masuk dalam rentang waktu 6 bulan terakhir
        $stokMasuk = InventoryLog::where('type', 'deposit') // Filter hanya stok masuk
            ->whereBetween('created_at', [$sixMonthsAgo, $today])
            ->get();

        // ambil data qty per bulanny, jika tidak ada data, isi dengan 0
        $qtyPerMonth = [];

        // Loop melalui rentang waktu 6 bulan terakhir
        for ($i = 0; $i < 6; $i++) {
            // Hitung tanggal untuk bulan ini
            $date = Carbon::now()->subMonths($i)->startOfMonth();

            // Format tanggal menjadi nama bulan dalam bahasa Inggris
            $key = $date->formatLocalized('%B');

            // Jika belum ada data stok masuk untuk bulan ini, set jumlahnya menjadi 0
            $qtyPerMonth[$key] = 0;
        }

        // Group data stok masuk berdasarkan bulan dan hitung jumlah qty untuk setiap bulan
        $stokMasuk = $stokMasuk->groupBy(function ($item) {
            return $item->created_at->formatLocalized('%B');
        })->map(function ($item) {
            return $item->sum('Qty');
        })->toArray();

        // Gabungkan data qty per bulan dengan data yang dihitung sebelumnya
        $qtyPerMonth = array_merge($qtyPerMonth, $stokMasuk);

        // Urutkan array berdasarkan kunci bulan
        uksort($qtyPerMonth, function ($a, $b) {
            return DateTime::createFromFormat('F', $a) > DateTime::createFromFormat('F', $b) ? 1 : -1;
        });

        return response()->json($qtyPerMonth, 200);
    }
    public function getDataAlatBulan()
    {
        // Mengambil tanggal saat ini
        $today = now();

        // Menghitung tanggal 6 bulan yang lalu dari sekarang
        $sixMonthsAgo = clone $today;
        $sixMonthsAgo->subMonths(6);

        // Mengambil data stok masuk dalam rentang waktu 6 bulan terakhir
        $stokMasuk = AlatLog::where('type', 'deposit') // Filter hanya stok masuk
            ->whereBetween('created_at', [$sixMonthsAgo, $today])
            ->get();

        // ambil data qty per bulanny, jika tidak ada data, isi dengan 0
        $qtyPerMonth = [];

        // Loop melalui rentang waktu 6 bulan terakhir
        for ($i = 0; $i < 6; $i++) {
            // Hitung tanggal untuk bulan ini
            $date = Carbon::now()->subMonths($i)->startOfMonth();

            // Format tanggal menjadi nama bulan dalam bahasa Inggris
            $key = $date->formatLocalized('%B');

            // Jika belum ada data stok masuk untuk bulan ini, set jumlahnya menjadi 0
            $qtyPerMonth[$key] = 0;
        }

        // Group data stok masuk berdasarkan bulan dan hitung jumlah qty untuk setiap bulan
        $stokMasuk = $stokMasuk->groupBy(function ($item) {
            return $item->created_at->formatLocalized('%B');
        })->map(function ($item) {
            return $item->sum('Qty');
        })->toArray();

        // Gabungkan data qty per bulan dengan data yang dihitung sebelumnya
        $qtyPerMonth = array_merge($qtyPerMonth, $stokMasuk);

        // Urutkan array berdasarkan kunci bulan
        uksort($qtyPerMonth, function ($a, $b) {
            return DateTime::createFromFormat('F', $a) > DateTime::createFromFormat('F', $b) ? 1 : -1;
        });

        return response()->json($qtyPerMonth, 200);
    }
    public function getDataConsumableBulan()
    {
        // Mengambil tanggal saat ini
        $today = now();

        // Menghitung tanggal 6 bulan yang lalu dari sekarang
        $sixMonthsAgo = clone $today;
        $sixMonthsAgo->subMonths(6);

        // Mengambil data stok masuk dalam rentang waktu 6 bulan terakhir
        $stokMasuk = ConsumableLog::where('type', 'deposit') // Filter hanya stok masuk
            ->whereBetween('created_at', [$sixMonthsAgo, $today])
            ->get();

        // ambil data qty per bulanny, jika tidak ada data, isi dengan 0
        $qtyPerMonth = [];

        // Loop melalui rentang waktu 6 bulan terakhir
        for ($i = 0; $i < 6; $i++) {
            // Hitung tanggal untuk bulan ini
            $date = Carbon::now()->subMonths($i)->startOfMonth();

            // Format tanggal menjadi nama bulan dalam bahasa Inggris
            $key = $date->formatLocalized('%B');

            // Jika belum ada data stok masuk untuk bulan ini, set jumlahnya menjadi 0
            $qtyPerMonth[$key] = 0;
        }

        // Group data stok masuk berdasarkan bulan dan hitung jumlah qty untuk setiap bulan
        $stokMasuk = $stokMasuk->groupBy(function ($item) {
            return $item->created_at->formatLocalized('%B');
        })->map(function ($item) {
            return $item->sum('Qty');
        })->toArray();

        // Gabungkan data qty per bulan dengan data yang dihitung sebelumnya
        $qtyPerMonth = array_merge($qtyPerMonth, $stokMasuk);

        // Urutkan array berdasarkan kunci bulan
        uksort($qtyPerMonth, function ($a, $b) {
            return DateTime::createFromFormat('F', $a) > DateTime::createFromFormat('F', $b) ? 1 : -1;
        });

        return response()->json($qtyPerMonth, 200);
    }

    public function getDataObatRingkas()
    {
        $data = InventoryObat::select('id', 'nama_obat', 'stok')->get();
        // dimana stoknya sama dengan 0
        $zerostock = $data->where('stok', 0)->count();
        // dimana stoknya kurang dari sama dengan 5 dan lebih dari 0
        $lowstock = $data->where('stok', '<=', 5)->where('stok', '>', 0)->count();
        $atstock = $data->where('stok', '>', 5)->count();

        $data = [
            'atstock' => $atstock,
            'lowstock' => $lowstock,
            'zerostock' => $zerostock,
        ];
        return response()->json($data, 200);
    }
    public function getDataAlatRingkas()
    {
        $data = InventoryAlat::select('id', 'nama_alat', 'stok')->get();
        // dimana stoknya sama dengan 0
        $zerostock = $data->where('stok', 0)->count();
        // dimana stoknya kurang dari sama dengan 5 dan lebih dari 0
        $lowstock = $data->where('stok', '<=', 5)->where('stok', '>', 0)->count();
        $atstock = $data->where('stok', '>', 5)->count();

        $data = [
            'atstock' => $atstock,
            'lowstock' => $lowstock,
            'zerostock' => $zerostock,
        ];
        return response()->json($data, 200);
    }
    public function getDataConsumableRingkas()
    {
        $data = InventoryConsumable::select('id', 'nama_alat', 'stok')->get();
        // dimana stoknya sama dengan 0
        $zerostock = $data->where('stok', 0)->count();
        // dimana stoknya kurang dari sama dengan 5 dan lebih dari 0
        $lowstock = $data->where('stok', '<=', 5)->where('stok', '>', 0)->count();
        $atstock = $data->where('stok', '>', 5)->count();

        $data = [
            'atstock' => $atstock,
            'lowstock' => $lowstock,
            'zerostock' => $zerostock,
        ];
        return response()->json($data, 200);
    }

    public function getSk()
    {
        // Mengambil 5 surat keterangan berobat terakhir dan mengambil nama pasiennya
        $skb = SuratKeteranganBerobat::all();
        $sksa = SuratKeteranganSakit::all();
        $skse = SuratKeteranganSehat::all();
        $sr = SuratRujukan::all();

        $data = [
            'skb' => $skb->count(),
            'sksa' => $sksa->count(),
            'skse' => $skse->count(),
            'sr' => $sr->count(),
        ];

        // Menghapus duplikasi nama pasien

        return response()->json($data, 200);
    }
    public function getSks()
    {
        // Mengambil 5 surat keterangan sakit terakhir dan mengambil nama pasiennya
        $sks = SuratKeteranganSakit::orderBy('created_at', 'desc')->take(5)->with('pasien')->get();

        // Menghitung jumlah surat berdasarkan pasien_id
        $countByPasien = SuratKeteranganSakit::select('pasien_id', DB::raw('count(*) as total'))
            ->groupBy('pasien_id')
            ->pluck('total', 'pasien_id');

        // Menyusun hasil akhir
        $result = $sks->map(function ($item) use ($countByPasien) {
            return [
                'nama_pasien' => $item->pasien->name,
                'jumlah_surat' => $countByPasien[$item->pasien_id]
            ];
        });

        // Menghapus duplikasi nama pasien
        $uniqueResult = $result->unique('nama_pasien')->values();

        return response()->json($uniqueResult, 200);
    }
    public function getSr()
    {
        // Mengambil 5 surat keterangan berobat terakhir dan mengambil nama pasiennya
        $sr = SuratRujukan::orderBy('created_at', 'desc')->take(5)->with('pasien')->get();

        // Menghitung jumlah surat berdasarkan pasien_id
        $countByPasien = SuratRujukan::select('pasien_id', DB::raw('count(*) as total'))
            ->groupBy('pasien_id')
            ->pluck('total', 'pasien_id');

        // Menyusun hasil akhir
        $result = $sr->map(function ($item) use ($countByPasien) {
            return [
                'nama_pasien' => $item->pasien->name ?? 'Tidak diketahui',
                'jumlah_surat' => $countByPasien[$item->pasien_id] ?? 0
            ];
        });

        // Menghapus duplikasi nama pasien
        $uniqueResult = $result->unique('nama_pasien')->values();

        return response()->json($uniqueResult, 200);
    }


    public function getSuratUser()
    {
        // Mengambil data surat bersarkan user yang login
        $skb = SuratKeteranganBerobat::where('pasien_id', auth()->user()->id)->count();
        $sks = SuratKeteranganSakit::where('pasien_id', auth()->user()->id)->count();
        $sr = SuratRujukan::where('pasien_id', auth()->user()->id)->count();

        $data = [
            'skb' => $skb,
            'sks' => $sks,
            'sr' => $sr,
        ];
        return response()->json($data, 200);
    }

    public function getKonseling()
    {
        $fb = FeedbackBimbingan::all()->count();
        $ks = DataPsikolog::all()->count();
        // buatkan result
        $data = [
            'fb' => $fb,
            'ks' => $ks,
        ];

        return response()->json($data, 200);
    }

    public function getPenyakit(){
        $data = DB::table('rekam_medis')
        ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan'), 'diagnosa', DB::raw('COUNT(*) as jumlah'))
        ->groupBy('bulan', 'diagnosa')
        ->havingRaw('COUNT(*) > 1') // Hanya tampilkan diagnosa dengan total kejadian lebih dari satu kali
        ->orderBy('bulan')
        ->get();

    return response()->json($data);
    }
}
