<!DOCTYPE html>
<html>

<head>
  <style>
    body {
      font-family: sans-serif;
      font-size: 10px;
    }

    .kop {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      background: #f3f4f6;
      padding: 8px;
      border: 1px solid #000;
      text-transform: uppercase;
    }

    td {
      padding: 6px;
      border: 1px solid #000;
    }

    .status {
      font-weight: bold;
      text-transform: uppercase;
    }
  </style>
</head>

<body>
  <div class="kop">
    <h2 style="margin:0">REKAPITULASI IZIN REKOMENDASI STATISTIK</h2>
    <p style="margin:0">Provinsi Sulawesi Tengah</p>
  </div>

  <table>
    <thead>
      <tr>
        <th width="20">No</th>
        <th>Instansi Pengusul</th>
        <th>Nama Kegiatan / Tabel</th>
        <th>Kategori</th>
        <th width="70">Tgl Pengajuan</th>
        <th width="60">Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data as $index => $row)
        <tr>
          <td align="center">{{ $index + 1 }}</td>
          <td>{{ $row->perangkatDaerah->nama_opd ?? 'N/A' }}</td>
          <td>{{ $row->table_name }}</td>
          <td align="center">{{ $row->category }}</td>
          <td align="center">{{ $row->created_at->format('d/m/Y') }}</td>
          <td align="center" class="status">
            {{ $row->status }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>