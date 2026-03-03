<!DOCTYPE html>
<html>
<head>
    <title>Laporan Statistik Sektoral</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA STATISTIK SEKTORAL</h2>
        <h3>{{ $data->recommendation->table_name }}</h3>
        <p>OPD: {{ $data->user->name }} | Tahun: {{ $data->tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                @foreach(array_keys($data->isi_data[0] ?? []) as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data->isi_data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                @foreach($row as $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>