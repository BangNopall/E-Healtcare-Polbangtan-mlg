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
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>LAPORAN KONSULTASI KONSELING</td>
            <td></td>
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
            <td></td>
        </tr>
        <tr>
            <th>#</th>
            <th>TANGGAL</th>
            <th>NIM</th>
            <th>NAMA</th>
            <th>METODE</th>
            <th>DIAGNOSA</th>
            <th>KELUHAN</th>
        </tr>
        @foreach ($konsul as $ks)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $ks->created_at->format('F Y') }}</td>
                <td>
                    @if ($ks->user->cdmi_complete == 1)
                        {{ $ks->user->getCDMI->nim }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $ks->user->name }}</td>
                <td>{{ $ks->metode_psikologi }}</td>
                <td>{{ $ks->diagnosa }}</td>
                <td>{{ $ks->keluhan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
