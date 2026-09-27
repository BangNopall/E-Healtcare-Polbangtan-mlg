<table>
    <thead>
        <tr>
            <th>BULAN : {{ $monthName }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>LAPORAN REKAM MEDIS</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>#</th>
            <th>TANGGAL</th>
            <th>NIM/NIK</th>
            <th>NAMA PASIEN</th>
            <th>DIAGNOSA</th>
            <th>KELUHAN</th>
            <th>TERAPI / TINDAKAN</th>
        </tr>
        @if (isset($rekammedis) && count($rekammedis) > 0)
            @foreach ($rekammedis as $rm)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ isset($rm->created_at) ? $rm->created_at->format('d/m/Y') : '-' }}</td>
                    <td>{{ \App\Helpers\SecurityHelper::sanitizeSpreadsheetCell($rm->user->nim ?? $rm->user->nik ?? '-') }}</td>
                    <td>{{ \App\Helpers\SecurityHelper::sanitizeSpreadsheetCell($rm->user->name ?? '-') }}</td>
                    <td>{{ \App\Helpers\SecurityHelper::sanitizeSpreadsheetCell($rm->diagnosa ?? '-') }}</td>
                    <td>{{ \App\Helpers\SecurityHelper::sanitizeSpreadsheetCell($rm->keluhan ?? '-') }}</td>
                    <td>{{ \App\Helpers\SecurityHelper::sanitizeSpreadsheetCell($rm->tindakan ?? '-') }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
