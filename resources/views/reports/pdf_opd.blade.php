<!DOCTYPE html>
<html>

<head>
  <style>
    body {
      font-family: sans-serif;
      font-size: 11px;
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
      background: #f2f2f2;
      padding: 10px;
      border: 1px solid #000;
      text-transform: uppercase;
    }

    td {
      padding: 8px;
      border: 1px solid #000;
    }

    .footer {
      margin-top: 30px;
      text-align: right;
    }
  </style>
</head>

<body>
  <div class="kop">
    <h2 style="margin:0">PEMERINTAH PROVINSI SULAWESI TENGAH</h2>
    <p style="margin:0">SISTEM INFORMASI DATA STATISTIK (SITATIK)</p>
  </div>

  <h3 style="text-align:center; text-transform:uppercase">LAPORAN MASTER PERANGKAT DAERAH</h3>

  <table>
    <thead>
      <tr>
        <th width="30">No</th>
        <th width="100">Kode</th>
        <th>Nama Perangkat Daerah</th>
        <th>Alias</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data as $index => $row)
        <tr>
          <td align="center">{{ $index + 1 }}</td>
          <td align="center">{{ $row->kode_opd }}</td>
          <td>{{ $row->nama_opd }}</td>
          <td align="center">{{ $row->alias_opd ?? '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="footer">
    Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
  </div>
</body>

</html>