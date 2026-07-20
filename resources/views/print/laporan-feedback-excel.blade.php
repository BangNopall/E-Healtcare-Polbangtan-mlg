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
                        {{ $d->siswa->getCDMI->nim }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $d->siswa->name }}</td>
                <td>{{ $d->senso->name }}</td>
                <td>{{ $d->jadwal->materi }}</td>
                <td>{{ $d->feedback }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
