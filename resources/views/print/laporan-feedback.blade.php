<!DOCTYPE html>
<html lang="en">

<head>
    <title>PDF REPORT</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<style>
    body {
        font-family: sans-serif;
    }

    .mt-3 {
        margin-top: 3rem;
    }

    .cwd {
        width: 90%;
        margin: 0 auto;
    }

    .te {
        font-weight: 600;
        color: #000;
    }

    .pa {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .justify-content-center {
        justify-content: center;
        display: flex;
    }

    table {
        border-collapse: collapse;
    }

    .table {
        margin-bottom: 1rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.281);
        color: #212529;
        min-width: 1000px !important;
        width: 100%;
        background: #fff;
    }

    .table td,
    .table th {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    .table tbody+tbody {
        border-top: 2px solid #dee2e6;
    }

    .table thead.thead-primary {
        background: #227442;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
        border: none;
        padding: 15px 25px;
        font-size: 15px;
        color: #fff;
    }

    .table tbody tr {
        margin-bottom: 10px;
    }

    .table tbody td,
    .table tbody th {
        border: none;
        padding: 20px 30px;
        border-bottom: 3px solid #f8f9fd;
        font-size: 14px;
    }
</style>

<body>
    <section class="mt-3">
        <div class="cwd">
            <div class="row justify-content-center">
                <h2>E-Klinik Polbangtan-mlg</h2>
            </div>
            <div class="te">Laporan Bimbingan Konseling</div>
            <div class="pa">
                Bulan : {{ $monthName }}
            </div>
            <table class="table">
                <thead class="thead-primary">
                    <tr>
                        <th>#</th>
                        <th>JADWAL</th>
                        <th>NIM</th>
                        <th>NAMA</th>
                        <th>SENSUH</th>
                        <th>JUDUL</th>
                        <th>FEEDBACK</th>
                    </tr>
                </thead>
                <tbody>
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
                            <td>{{ $d->siswa->name }}</td>
                            <td>{{ $d->senso->name }}</td>
                            <td>{{ $d->jadwal->materi }}</td>
                            <td>{{ $d->feedback }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</body>

</html>
