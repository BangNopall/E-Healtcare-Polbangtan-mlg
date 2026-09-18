<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class laporanRM implements FromView
{
    protected $rekammedis;
    protected $monthName;

    public function __construct($rekammedis, $monthName)
    {
        $this->rekammedis = $rekammedis;
        $this->monthName = $monthName;
    }

    public function view(): View
    {
        return view('print.laporan-rm-excel', [
            'rekammedis' => $this->rekammedis,
            'monthName' => $this->monthName
        ]);
    }
}
