<!DOCTYPE html>
<html>

<head>
    <title>Laporan Statistik Sektoral</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
            color: #555;
        }

        .header p {
            margin: 0;
            font-size: 11px;
            font-style: italic;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN DATA STATISTIK SEKTORAL</h2>
        <h3>{{ $data->recommendation->table_name }}</h3>
        <p>OPD: {{ $data->user->perangkatDaerah->nama_opd ?? $data->user->name }} | Tahun: {{ $data->tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                @foreach(array_keys($data->isi_data[0] ?? []) as $header)
                    {{-- Sembunyikan header jika namanya adalah 'NO' --}}
                    @if(strtolower($header) !== 'no')
                        <th>{{ $header }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data->isi_data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @foreach($row as $key => $value)
                        {{-- Sembunyikan isi kolom jika kuncinya adalah 'NO' --}}
                        @if(strtolower($key) !== 'no')
                            <td>{{ $value ?? '-' }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>

</html>