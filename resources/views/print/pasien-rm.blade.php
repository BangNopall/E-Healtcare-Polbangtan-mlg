<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Riwayat Rekam Medis Pasien</title>
    <style>
        /* Tambahkan CSS Anda di sini */
        .td-center-atas,
        .title {
            text-align: left;
        }

        .kotak,
        .kotak1 {
            background: #fff;
            border: 2px solid #dcdcdc;
            border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            margin: 20px;
        }

        .container {
            width: auto;
            height: auto;
            margin: 28px auto;
        }

        .kotak {
            margin: 28px auto auto;
            padding: 10px;
        }

        .kotak1 {
            margin: 12px auto auto;
            padding: 12px;
        }

        .title-profil {
            line-height: 18px;
        }

        .garis {
            border: 2px solid #dcdcdc;
            border-top-width: 0;
            border-left-width: 0;
            border-right-width: 0;
            margin-bottom: 4px;
            margin-top: 4px;
        }

        .profil {
            display: flex;
            flex-direction: row;
            gap: 12px;
        }

        .td-bawah,
        .td-center,
        .td-center-atas,
        .td-nocenter {
            padding: 8px;
            vertical-align: inherit;
            display: table-cell;
        }

        .profil .foto-profil {
            width: 150px;
            height: 190px;
            border-radius: 8px;
            background: #cfcfcf;
        }

        .profil .foto-profil img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .profil .tabel {
            color: #2b2b2b;
        }

        table {
            line-height: 1.5;
            font-size: 12px;
        }

        .title-kategori {
            font-size: 14px;
            font-weight: 400;
            margin-top: 10px;
            margin-bottom: 8px;
        }

        .kotak-tabel,
        sub {
            border-radius: 4px;
            margin-top: 8px;
        }

        .tabel-pelanggaran {
            border-radius: 4px;
            width: 100%;
            font-size: 11px;
            color: rgb(107 114 128 / 1);
            border-collapse: collapse;
        }

        .head-pelanggaran {
            font-size: 12px;
            color: rgb(55 65 81 / 1);
            text-transform: uppercase;
            background: rgb(255 237 213 / 1);
        }

        .td-center-atas {
            font-weight: 700;
            text-align: -internal-center;
            white-space: nowrap;
        }

        .tr-pelanggaran {
            background: rgb(255 247 237 / 1);
            border: 2px solid #e0e0e0;
            border-top-width: 0;
            border-left-width: 0;
            border-right-width: 0;
        }

        .td-bawah,
        .td-center {
            font-weight: 400;
            white-space: wrap;
        }

        .td-center {
            text-align: left;
        }

        .td-bawah {
            width: 100%;
            text-align: left;
        }

        li {
            list-style-type: none;
            display: inline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="title">Riwayat Rekam Medis Pasien</h2>

        <!-- Profil Pasien -->
        <div class="kotak">
            <h4 class="title-profil">Profil Pasien</h4>
            <div class="garis"></div>
            <div class="profil">
                <div class="tabel">
                    <table>
                        <tbody>
                            <tr>
                                <td width="150">NIK</td>
                                <td>:</td>
                                <td>{{ $user->getDMTI->nik }}</td>
                            </tr>
                            <tr>
                                <td width="150">No. BPJS</td>
                                <td>:</td>
                                <td>{{ $user->getDMTI->no_bpjs }}</td>
                            </tr>
                            <tr>
                                <td width="150">Nama</td>
                                <td>:</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td width="150">Jenis Kelamin</td>
                                <td>:</td>
                                <td>{{ $user->getDMTI->jenis_kelamin }}</td>
                            </tr>
                            <tr>
                                <td width="150">No. Handphone</td>
                                <td>:</td>
                                <td>{{ $user->getDMTI->no_hp }}</td>
                            </tr>
                            <tr>
                                <td width="150">Tempat Lahir</td>
                                <td>:</td>
                                <td>{{ $user->getDMTI->tempat_kelahiran }}</td>
                            </tr>
                            <tr>
                                <td width="150">Tanggal Lahir</td>
                                <td>:</td>
                                <td>{{ $user->getDMTI->tanggal_lahir }}</td>
                            </tr>
                            <tr>
                                <td width="150">Usia</td>
                                <td>:</td>
                                <td>{{ \Carbon\Carbon::parse($user->tanggal_lahir)->age }} tahun</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rekam Medis Pasien -->
        <div class="kotak1">
            <h4 class="title-profil">Rekam Medis</h4>
            <div class="garis"></div>
            <div class="kotak-tabel">
                <table class="tabel-pelanggaran">
                    <thead class="head-pelanggaran">
                        <tr>
                            <td class="td-center-atas">Keluhan</td>
                            <td class="td-center-atas">Pemeriksaan Fisik</td>
                            <td class="td-center-atas">Diagnosa</td>
                            <td class="td-center-atas">Terapi</td>
                            <td class="td-center-atas">Intervensi</td>
                            <td class="td-center-atas">Rawat Jalan</td>
                            <td class="td-center-atas">Rujukan</td>
                            <td class="td-center-atas">Rawat Inap</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekamMedis as $rm)
                            <tr class="tr-pelanggaran">
                                <td class="td-center">{{ $rm->keluhan }}</td>
                                <td class="td-center">{{ $rm->pemeriksaan }}</td>
                                <td class="td-center">{{ $rm->diagnosa }}</td>
                                <td class="td-center">
                                    @if ($rm->withObat)
                                        @foreach ($rm->tindakan['nama_obat'] as $index => $namaObat)
                                            <li>{{ $namaObat }} ({{ $rm->tindakan['jumlah_obat'][$index] }})</li>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="td-center">
                                    @if ($rm->withAlat)
                                        @foreach ($rm->tindakan['nama_alat'] as $index => $namaAlat)
                                            <li>{{ $namaAlat }} ({{ $rm->tindakan['jumlah_alat'][$index] }})</li>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="td-center">{{ $rm->rawatjalan }}</td>
                                <td class="td-center">{{ $rm->rs_name_rujukan }}</td>
                                <td class="td-center">{{ $rm->rs_name_rawatinap }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
