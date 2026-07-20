<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\SuratRujukan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\SuratKeteranganSakit;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratKeteranganBerobat;
use App\Models\SuratKeteranganSehat;

class KesehatanUserController extends Controller
{
    public function dashboard()
    {
        $user = User::all();

        $pasien = $user->where('role', 'Mahasiswa')->count() + $user->where('role', 'Karyawan')->count();
        $dokter = $user->where('role', 'Dokter')->count();
        return view('kesehatan.user.dashboard', compact('pasien', 'dokter'));
    }

    public function riwayatLaporan()
    {
        // mengambil data surat laporan berobat yang pasien idnya sesuai dengan user yang sedang login
        $SuratKeteranganBerobat = SuratKeteranganBerobat::where('pasien_id', auth()->user()->id)->get();
        // dd($SuratKeteranganBerobat);
        $data['skbs'] = $SuratKeteranganBerobat;

        $SuratKeteranganSakit = SuratKeteranganSakit::where('pasien_id', auth()->user()->id)->get();
        $data['skss'] = $SuratKeteranganSakit;
        
        $SuratKeteranganSehat = SuratKeteranganSehat::where('pasien_id', auth()->user()->id)->get();
        $data['skses'] = $SuratKeteranganSehat;

        $SuratRujukan = SuratRujukan::where('pasien_id', auth()->user()->id)->get();
        $data['srss'] = $SuratRujukan;

        return view('kesehatan.user.riwayat-laporan', $data);
    }

    public function showSuratKeteranganObat($nomor_surat)
    {
        // membuat user hanya dapat mengakses surat yang sesuai dengan id user
        $surat = SuratKeteranganBerobat::where('nomor_surat', $nomor_surat)->first();
        if (!$surat) {
            return back()->with('error', 'Surat tidak ditemukan');
        }

        $user = User::find($surat->pasien_id);

        // Pastikan user yang login hanya dapat mengakses surat milik mereka sendiri
        if (Auth::id() !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke surat ini');
        }

        $data['surat'] = $surat;

        return view('kesehatan.user.detail-skb', $data);
    }
    public function showSuratKeteranganSehat($nomor_surat)
    {
        // membuat user hanya dapat mengakses surat yang sesuai dengan id user
        $surat = SuratKeteranganSehat::where('nomor_surat', $nomor_surat)->first();
        if (!$surat) {
            return back()->with('error', 'Surat tidak ditemukan');
        }

        $user = User::find($surat->pasien_id);

        // Pastikan user yang login hanya dapat mengakses surat milik mereka sendiri
        if (Auth::id() !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke surat ini');
        }

        $data['surat'] = $surat;

        return view('kesehatan.user.detail-skse', $data);
    }
    public function showSuratKeteranganSakit($nomor_surat)
    {
        $surat = SuratKeteranganSakit::where('nomor_surat', $nomor_surat)->first();
        if (!$surat) {
            return back()->with('error', 'Surat tidak ditemukan');
        }

        $user = User::find($surat->pasien_id);

        // Pastikan user yang login hanya dapat mengakses surat milik mereka sendiri
        if (Auth::id() !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke surat ini');
        }

        $data['surat'] = $surat;

        return view('kesehatan.user.detail-sks', $data);
    }
    public function showSuratRujukan($nomor_surat)
    {
        $surat = SuratRujukan::where('nomor_surat', $nomor_surat)->first();
        if (!$surat) {
            return back()->with('error', 'Surat tidak ditemukan');
        }

        $user = User::find($surat->pasien_id);

        // Pastikan user yang login hanya dapat mengakses surat milik mereka sendiri
        if (Auth::id() !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke surat ini');
        }

        $data['surat'] = $surat;

        return view('kesehatan.user.detail-sr', $data);
    }

    public function printSuratKeteranganObat($id)
    {
        $surat = SuratKeteranganBerobat::find($id);
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        if ($surat->withObat == 1) {
            $data['obatLogs'] = $surat->obatLogs;
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('print.skb', $data)->setPaper('a4', 'portrait');

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat->nomor_surat) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return redirect()->route('kesehatan.kamera')->with('success', 'Berhasil Mencetak Surat Keterangan Berobat');

            // setelah download kembali ke view kamera
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Surat Keterangan Berobat: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Surat Keterangan Berobat' . $th->getMessage());
        }
    }
    public function printSuratKeteranganSehat($id)
    {
        $surat = SuratKeteranganSehat::find($id);
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        if ($surat->withObat == 1) {
            $data['obatLogs'] = $surat->obatLogs;
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('print.skse', $data)->setPaper('a4', 'portrait');

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat->nomor_surat) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return redirect()->route('kesehatan.kamera')->with('success', 'Berhasil Mencetak Surat Keterangan Berobat');

            // setelah download kembali ke view kamera
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Surat Keterangan Sehat: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Surat Keterangan Sehat' . $th->getMessage());
        }
    }

    public function printSuratKeteranganSakit($id)
    {
        $surat = SuratKeteranganSakit::find($id);
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        if ($surat->withObat == 1) {
            $data['obatLogs'] = $surat->obatLogs;
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('print.sks', $data)->setPaper('a4', 'portrait');

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat->nomor_surat) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return redirect()->route('kesehatan.kamera')->with('success', 'Berhasil Mencetak Surat Keterangan Berobat');

            // setelah download kembali ke view kamera
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Surat Keterangan Sakit: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Surat Keterangan Sakit' . $th->getMessage());
        }
    }

    public function printSuratRujukan($id)
    {
        $surat = SuratRujukan::find($id);
        $data['surat'] = $surat;
        $data['user'] = User::find($surat->pasien_id);

        try {
            // Generate PDF
            $pdf = Pdf::loadView('print.sr', $data)->setPaper('a4', 'portrait');

            // Define temporary file path using $surat->nomor_surat
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat->nomor_surat) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Store PDF to temporary file
            $pdf->save($filePath);

            // Return download response and delete the file afterwards dan kembali ke halaman sebelumnya
            return response()->download($filePath)->deleteFileAfterSend(true);
            // return redirect()->route('kesehatan.kamera')->with('success', 'Berhasil Mencetak Surat Keterangan Berobat');

            // setelah download kembali ke view kamera
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Surat Rujukan: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Surat Rujukan' . $th->getMessage());
        }
    }
}
