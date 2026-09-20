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
            <td>LAPORAN BIMBINGAN KONSELING</td>
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
            <th>JADWAL</th>
            <th>NIM</th>
            <th>NAMA</th>
            <th>SENSUH</th>
            <th>JUDUL</th>
            <th>FEEDBACK</th>
        </tr>
        @foreach ($data as $d)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $d->jadwal->created_at->format('F Y') }}</td>
                <td>
                    @if ($d->siswa->cdmi_complete == 1)
                        {{ $d->siswa->nim }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ preg_match('/^[=+\-@\t\r]/', (string)$d->siswa->name) ? "'" . $d->siswa->name : $d->siswa->name }}</td>
                <td>{{ preg_match('/^[=+\-@\t\r]/', (string)$d->senso->name) ? "'" . $d->senso->name : $d->senso->name }}</td>
                <td>{{ preg_match('/^[=+\-@\t\r]/', (string)$d->jadwal->materi) ? "'" . $d->jadwal->materi : $d->jadwal->materi }}</td>
                <td>{{ preg_match('/^[=+\-@\t\r]/', (string)$d->feedback) ? "'" . $d->feedback : $d->feedback }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
