<?php

namespace App\Http\Controllers;

use App\Models\FeedbackBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\User;
use App\Models\AlatLog;
use App\Models\ObatLog;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use App\Models\ConsumableLog;
use App\Models\DataPsikolog;
use App\Models\InventoryAlat;
use App\Models\InventoryObat;
use App\Models\InventoryConsumable;
use App\Models\PresensiBimbingan;

class DashboardController extends Controller
{
    public function inventaris()
    {
        // data item inventaris
        $obat = InventoryObat::all();
        $alat_terpakai = InventoryAlat::all();
        $alat_tersisa = InventoryConsumable::all();

        // logs inventaris
        $obatlogs = InventoryLog::all();
        $obatlogs2 = ObatLog::all();
        $alatlogs = AlatLog::all();
        $consumablelogs = ConsumableLog::all();

        // jumlah item inventaris
        $data_obat = $obat->count();
        $data_alat_terpakai = $alat_terpakai->count();
        $data_alat_tersisa = $alat_tersisa->count();
        // total item inventaris
        $total_item = $data_obat + $data_alat_terpakai + $data_alat_tersisa;

        // kuantitas obat
        $stok_obat = $obat->sum('stok');
        $stok_alat_terpakai = $alat_terpakai->sum('stok');
        $stok_alat_tersisa = $alat_tersisa->sum('stok');
        // menjumlahkan semua kuatitas inventaris
        $total_stok = $stok_obat + $stok_alat_terpakai + $stok_alat_tersisa;

        // obatlogs bulan ini
        $obatlogs_withdraw = $obatlogs->where('type', 'withdraw')->where('created_at', '>=', now()->startOfMonth());
        $obatlogs_withdraw_qty = $obatlogs_withdraw->sum('Qty');
        $obatlogs_deposit = $obatlogs->where('type', 'deposit')->where('created_at', '>=', now()->startOfMonth());
        $obatlogs_deposit_qty = $obatlogs_deposit->sum('Qty');
        $obatlogs2_withdraw = $obatlogs2->where('type', 'withdraw')->where('created_at', '>=', now()->startOfMonth());
        $obatlogs2_withdraw_qty = $obatlogs2_withdraw->sum('Qty');

        // alatlogs
        $alatlogs_withdraw = $alatlogs->where('type', 'withdraw')->where('created_at', '>=', now()->startOfMonth());
        $alatlogs_deposit = $alatlogs->where('type', 'deposit')->where('created_at', '>=', now()->startOfMonth());
        $alatlogs_deposit_qty = $alatlogs_deposit->sum('Qty');
        $alatlogs_withdraw_qty = $alatlogs_withdraw->sum('Qty');

        // consumablelogs
        $consumablelogs_withdraw = $consumablelogs->where('type', 'withdraw')->where('created_at', '>=', now()->startOfMonth());
        $consumablelogs_deposit = $consumablelogs->where('type', 'deposit')->where('created_at', '>=', now()->startOfMonth());
        $consumablelogs_withdraw_qty = $consumablelogs_withdraw->sum('Qty');
        $consumablelogs_deposit_qty = $consumablelogs_deposit->sum('Qty');

        // total semua withdraw logs
        $total_withdraw = $obatlogs_withdraw_qty + $obatlogs2_withdraw_qty + $alatlogs_withdraw_qty + $consumablelogs_withdraw_qty;
        // total semua deposit logs
        $total_deposit = $obatlogs_deposit_qty + $alatlogs_deposit_qty + $consumablelogs_deposit_qty;

        // rata rata persen
        if ($total_deposit == 0) {
            $rata_stockout = 0;
            $rata_stockin = 0;
        } else {
            $rata_stockout = ($total_withdraw / $total_deposit) * 100;
            $rata_stockin = ($total_deposit /  ($total_deposit + $total_withdraw)) * 100;
        }

        function custom_round($value)
        {
            $decimal_part = $value - floor($value);
            if ($decimal_part >= 0.6) {
                return ceil($value);
            } else {
                return floor($value);
            }
        }

        $rata_stockout_bulat = custom_round($rata_stockout);
        $rata_stock_in_bulat = custom_round($rata_stockin);

        return view('inventaris.dashboard', compact('total_item', 'total_stok', 'rata_stockout_bulat', 'rata_stock_in_bulat', 'data_obat', 'data_alat_terpakai', 'data_alat_tersisa'));
    }

    public function konseling()
    {
        $user = User::all();    
        $feedback = FeedbackBimbingan::all()->count();
        $konsultasi = DataPsikolog::all()->count();
        $jadwal = JadwalBimbingan::all();
        $presensi = PresensiBimbingan::all();

        $mahasiswa = $user->where('role', 'Mahasiswa')->count();
        $psikiater = $user->where('role', 'Psikiater')->count();

        // ambil jadwal bimbingan yang tanggalnya hari ini       
        $materitoday = $jadwal->where('tanggal', now()->format('Y-m-d'))->first();

        // ambil jadwal bimibingan 3 hari terakhir
        $lastjadwal = $jadwal->where('tanggal', '>=', now()->subDays(5)->format('Y-m-d'))
            ->where('tanggal', '<=', now()->format('Y-m-d'))
            ->sortByDesc('tanggal')
            ->take(5);

        // presensi senso hari ini
        $sakit = $presensi->where('status', 'Sakit')->where('tanggal_presensi', now()->format('Y-m-d'))->count();
        $izin = $presensi->where('status', 'Izin')->where('tanggal_presensi', now()->format('Y-m-d'))->count();
        $alpha = $presensi->where('status', 'Alpha')->where('tanggal_presensi', now()->format('Y-m-d'))->count();

        return view('konseling.dashboard', compact('mahasiswa', 'psikiater', 'feedback', 'konsultasi', 'jadwal', 'materitoday', 'lastjadwal', 'sakit', 'izin', 'alpha'));
    }
}
