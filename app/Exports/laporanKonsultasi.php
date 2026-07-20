<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class laporanKonsultasi implements FromView
{
    protected $konsul;
    protected $monthName;

    public function __construct($konsul, $monthName)
    {
        $this->konsul = $konsul;
        $this->monthName = $monthName;
    }

    public function view(): View
    {
        return view('print.laporan-konsultasi-excel', [
            'konsul' => $this->konsul,
            'monthName' => $this->monthName
        ]);
    }
}
