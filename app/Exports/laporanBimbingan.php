<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class laporanBimbingan implements FromView
{
    protected $data;
    protected $monthName;

    public function __construct($data, $monthName)
    {
        $this->data = $data;
        $this->monthName = $monthName;
    }

    public function view(): View
    {
        return view('print.laporan-feedback-excel', [
            'data' => $this->data,
            'monthName' => $this->monthName
        ]);
    }
}
