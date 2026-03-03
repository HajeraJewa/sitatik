<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
    }

    .info-section {
      margin-bottom: 15px;
      font-size: 11px;
    }

    /* TABEL HORIZONTAL (HEADER KE SAMPING) */
    .excel-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      /* Membagi lebar kolom secara merata */
    }

    .excel-table th {
      background-color: #d1d5db;
      /* Abu-abu Header Excel */
      border: 1px solid #000;
      padding: 8px;
      font-size: 10px;
      text-align: center;
      text-transform: uppercase;
      word-wrap: break-word;
    }

    .excel-table td {
      border: 1px solid #000;
      padding: 25px;
      /* Memberikan ruang kosong untuk simulasi isi data */
    }

    .footer {
      margin-top: 30px;
      width: 100%;
      font-size: 11px;
    }

    .signature {
      float: right;
      width: 200px;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="header">
    <h2 style="margin:0; font-size: 16px;">SITATIK - SATU DATA KOTA PALU</h2>
    <p style="margin:5px 0 0 0; font-size: 10px;">REKOMENDASI STRUKTUR DASAR TABEL (DOKUMEN KERANGKA)</p>
  </div>

  <div class="info-section">
    <p><strong>OPD PENGUSUL:</strong> {{ $rec->user->name }}</p>
    <p><strong>NAMA TABEL:</strong> {{ $rec->table_name }}</p>
    <p><strong>TANGGAL PERSETUJUAN:</strong> {{ $date }}</p>
  </div>

  <table class="excel-table">
    <thead>
      <tr>
        <th width="30">NO</th>

        @foreach($columns as $kolom)
          <th>{{ strtoupper(trim($kolom)) }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @for ($i = 1; $i <= 3; $i++)
        <tr>
          <td align="center" style="font-weight: bold; color: #999;">{{ $i }}</td>
          @foreach($columns as $kolom)
            <td></td>
          @endforeach
        </tr>
      @endfor
    </tbody>
  </table>

  <div class="footer">
    <div class="signature">
      <p>Palu, {{ $date }}</p>
      <p>Admin SITATIK,</p>
      <br><br><br>
      <p><strong>( ____________________ )</strong></p>
      <p style="font-size: 9px;">NIP. ...................................</p>
    </div>
  </div>
</body>

</html>