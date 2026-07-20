<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Data Inventaris Obat</title>
  </head>
  <style>
    body{font-family:Arial,sans-serif;margin:20px}table{width:100%;border-collapse:collapse;margin:20px 0;font-size:18px}thead tr{background-color:#009879;color:#fff;font-weight:700}td,th{padding:12px 15px}tbody tr:nth-child(2n){background-color:#f3f3f3}tbody tr:nth-child(odd){background-color:#e2e2e2}tbody tr:hover{background-color:#f1f1f1}
  </style>
  <body>
    <h1>Data Inventaris Obat</h1>
    <h3>Export pada {{$time}}</h3>
    <table>
      <thead>
        <tr>
          <td>Nama Item</td>
          <td>Item ID</td>
          <td>Kategori</td>
          <td>Kuantitas</td>
        </tr>
      </thead>
      <tbody>
        @foreach ($data as $d)
        <tr>
          <td>{{ $d->nama_obat }}</td>
          <td>{{ $d->kode_obat }}</td>
          <td>{{ $d->SatuanObat->nama_satuan }}</td>
          <td>{{ $d->stok }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </body>
</html>
