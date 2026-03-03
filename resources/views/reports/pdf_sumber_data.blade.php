<!DOCTYPE html>
<html>

<head>
  <style>
    body {
      font-family: sans-serif;
      font-size: 11px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
      text-transform: uppercase;
    }
  </style>
</head>

<body>
  <h2 style="text-align: center;">LAPORAN DAFTAR SUMBER DATA</h2>
  <p style="text-align: center;">(Berdasarkan Rekomendasi yang Telah Disetujui)</p>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Tabel / Sumber</th>
        <th>Instansi Pengusul</th>
        <th>Kategori</th>
        <th>Tanggal Disetujui</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data as $index => $row)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $row->table_name }}</td>
          <td>{{ $row->perangkatDaerah->nama_opd ?? 'N/A' }}</td>
          <td>{{ $row->category }}</td>
          <td>{{ $row->updated_at->format('d/m/Y') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>