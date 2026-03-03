<!DOCTYPE html>
<html>

<head>
  <style>
    body {
      font-family: 'Helvetica', sans-serif;
      font-size: 10px;
      color: #333;
    }

    .header {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    .title {
      font-size: 16px;
      font-weight: bold;
      text-transform: uppercase;
      margin: 0;
    }

    .subtitle {
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      margin: 5px 0 0 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th {
      background: #f2f2f2;
      padding: 10px 5px;
      border: 1px solid #000;
      text-transform: uppercase;
      font-size: 9px;
    }

    td {
      padding: 8px 5px;
      border: 1px solid #000;
      vertical-align: middle;
    }

    .text-center {
      text-align: center;
    }

    .footer {
      margin-top: 30px;
      text-align: right;
      font-style: italic;
      font-size: 8px;
    }
  </style>
</head>

<body>
  <div class="header">
    <p class="title">PEMERINTAH PROVINSI SULAWESI TENGAH</p>
    <p class="subtitle">DAFTAR PENGGUNA SISTEM SITATIK</p>
    <p style="margin: 5px 0 0 0;">Data Akun Operator Perangkat Daerah Aktif</p>
  </div>

  <table>
    <thead>
      <tr>
        <th width="30">NO</th>
        <th>PERANGKAT DAERAH (INSTANSI)</th>
        <th>EMAIL LOGIN</th>
        <th width="120">KOORDINAT (LAT, LONG)</th>
        <th width="100">TANGGAL REGISTRASI</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $index => $user)
        <tr>
          <td class="text-center">{{ $index + 1 }}</td>
          <td>
            <strong>{{ $user->perangkatDaerah->nama_opd ?? 'ADMIN / TANPA OPD' }}</strong>
            @if(isset($user->perangkatDaerah->kode_opd))
              <br><span style="font-size: 8px; color: #666;">Kode: {{ $user->perangkatDaerah->kode_opd }}</span>
            @endif
          </td>
          <td>{{ $user->email }}</td>
          <td class="text-center">
            {{ $user->latitude ?? '0' }}, {{ $user->longitude ?? '0' }}
          </td>
          <td class="text-center">
            {{ $user->created_at->translatedFormat('d/m/Y') }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">Data pengguna tidak ditemukan.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Dicetak secara otomatis melalui Sistem SITATIK pada: {{ now()->translatedFormat('d F Y H:i') }}
  </div>
</body>

</html>