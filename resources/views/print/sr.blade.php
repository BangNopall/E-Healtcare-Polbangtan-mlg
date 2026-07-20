<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
  </head>
  <style>
    * {
      font-family: Verdana, Arial, sans-serif;
    }
    h1, h2, h3, h4, h5, h6{
        margin: 0;
        text-align: center;
    }
    .table1{
        width: 100%;
        border-bottom: 2px solid #000;
    }
    img{
        border-radius: 15px;
        width: 100px;
        height: 100px;
    }
    .title{
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    p{
        text-align: justify;
        line-height: 1.5;
    }
    td{
        vertical-align: top;
    }
    .tdnotop{
        vertical-align: middle;
    }
  </style>
  <body>
    <table class="table1">
      <tr>
        <td class="tdnotop">
          <img src="https://raw.githubusercontent.com/BangNopall/coba-laravel/master/app/logo-klinik.jpeg" alt="" />
        </td>
        <td class="tdnotop">
          <h2>Politeknik Pembangunan dan Pertanian Malang</h2>
          <h3>Klinik Polbangtan-mlg</h3>
          <h3>Jl. Dr.cipto 144A bedali lawang 65200</h3>
        </td>
      </tr>
    </table>
    <div class="title">SURAT RUJUKAN</div>
    <table style="margin-top: 10px; width: 100%;">
        <tr>
            <td style="width: 100%;"></td>
            <td style="white-space: nowrap;">
                Yth. {{ $surat->nama_dokter }}<br>
                Di<br>
                {{ $surat->nama_rs }}
            </td>
        </tr>
    </table>
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="text-align: center;">Mohon pemeriksaan dan pengobatan lebih lanjut terhadap penderita</td>
        </tr>
    </table>
    <table style="margin-top: 10px; margin-bottom: 15px;">
        <tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $surat->nik }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $surat->nama_pasien }}</td>
            </tr>
            <tr>
                <td>Tempat/tgl lahir</td>
                <td>:</td>
                <td>{{ $surat->ttl }}</td>
            </tr>
            <tr>
                <td>Umur</td>
                <td>:</td>
                <td>{{ $surat->usia }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $surat->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td>No. Handphone</td>
                <td>:</td>
                <td>{{ $surat->no_hp }}</td>
            </tr>
        </tr>
    </table>
    <table>
        <tr>
            <td>Anamnese</td>
        </tr>
    </table>
    <table style="margin-top: 10px;">
        <tr>
            <tr>
                <td>Keluhan</td>
                <td>:</td>
                <td>{{ $surat->keluhan }}</td>
            </tr>
            <tr>
                <td style="white-space: nowrap;">Diagnosa sementara</td>
                <td>:</td>
                <td>{{ $surat->diagnosa }}</td>
            </tr>
            <tr>
                <td>Kasus</td>
                <td>:</td>
                <td>{{ $surat->kasus }}</td>
            </tr>
            <tr>
                <td>Terapi/obat yang telah disediakan</td>
                <td>:</td>
                <td>{{ $surat->tindakan }}</td>
            </tr>
        </tr>
    </table>
    <table style="margin-top: 5px;">
        <tr>
            <td>
                <p>Demikian surat rujukan ini kami kirim, kami mohon balasan atas surat rujukan ini. Atas perhatian Bapak/Ibu kami ucapkan terima kasih.</p>
            </td>
        </tr>
    </table>
    <table style="margin-top: 5px; width: 100%;">
        <tr>
            <td style="width: 100%;"></td>
            <td style="white-space: nowrap; text-align: center;">
                Malang, {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y') }}<br>
                Hormat Kami<br><br><br><br>
                <span style="font-weight: bold; text-decoration: underline;">_____________</span>
            </td>
        </tr>
    </table>
  </body>
</html>
