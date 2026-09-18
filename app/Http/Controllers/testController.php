<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class testController extends Controller
{
    public function qrcodebimbingan()
    {
        $user = Auth::user();
        $validatedQR = [
            'user_id' => $user->id,
        ];

        $json = json_encode($validatedQR);
        $QrCode = QrCode::size(300)->generate($json);

        return view('konseling.user.kodeqr-bimbingan', compact('QrCode'));
    }
    public function qrcodekonsultasi()
    {
        $user = Auth::user();
        $validatedQR = [
            'user_id' => $user->id,
        ];

        $json = json_encode($validatedQR);
        $QrCode = QrCode::size(300)->generate($json);

        return view('konseling.user.kodeqr-konsultasi', compact('QrCode'));
    }

    public function testprint(){
        $pdf = Pdf::loadView('print.pasien-rm');
        return $pdf->download('skb.pdf');
    }
}
