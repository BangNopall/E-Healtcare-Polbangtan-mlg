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
            <td>LAPORAN REKAM MEDIS PASIEN</td>
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
            <th>NIK</th>
            <th>NAMA</th>
            <th>POSISI</th>
            <th>DIAGNOSA/PENYAKIT</th>
            <th>INTERVENSI/OBAT</th>
        </tr>
        @foreach ($rekammedis as $rm)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $rm->created_at->format('d F Y') }}</td>
                <td>
                    @if ($rm->pasien->dmti_complete == 1)
                        {{ $rm->pasien->getDMTI->nik }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $rm->pasien->name }}</td>
                <td>{{ $rm->pasien->role }}</td>
                <td>{{ $rm->diagnosa }}</td>
                <td>
                    @if ($rm->withObat)
                        @foreach ($rm->tindakan['nama_obat'] as $index => $namaObat)
                            <li>{{ $namaObat }} ({{ $rm->tindakan['jumlah_obat'][$index] }})</li>
                        @endforeach
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
