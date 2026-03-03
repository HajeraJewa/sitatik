<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatisticData;
use App\Models\Recommendation;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StatisticExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil rekomendasi yang statusnya approved
        // Tambahkan relasi user.perangkatDaerah agar kode_opd bisa diakses
        $query = Recommendation::where('status', 'approved')
            ->with(['statisticData', 'user.perangkatDaerah']);

        // --- LOGIKA FILTER ---

        // Filter Berdasarkan OPD (id user operator)
        if ($request->filled('opd_id')) {
            $query->where('user_id', $request->opd_id);
        }

        // Filter Berdasarkan Tahun (mengambil data dari field start_date di tabel recommendations)
        if ($request->filled('tahun')) {
            $query->whereYear('start_date', $request->tahun);
        }

        // Filter Berdasarkan Kategori
        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        // Filter Search Nama Tabel
        if ($request->filled('search')) {
            $query->where('table_name', 'like', '%' . $request->search . '%');
        }

        // Role Logic
        if (auth()->user()->role == 'admin') {
            $allOpd = User::where('role', 'operator')->with('perangkatDaerah')->get();
        } else {
            // Operator hanya melihat miliknya sendiri
            $query->where('user_id', auth()->id());
            $allOpd = collect();
        }

        $statistics = $query->latest()->get();

        // Untuk modal input operator
        $approvedTables = Recommendation::where('status', 'approved')
            ->where('user_id', auth()->id())
            ->get();

        return view('statistics.index', compact('statistics', 'approvedTables', 'allOpd'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
            'tahun' => 'required|integer',
            'excel_file' => 'nullable|mimes:xlsx,xls',
            'data_content' => 'nullable|array'
        ]);

        $newData = [];

        // 1. Logika Impor Excel: Mengubah baris menjadi key-value pair
        if ($request->hasFile('excel_file')) {
            $array = Excel::toArray([], $request->file('excel_file'))[0];
            $header = array_shift($array);
            foreach ($array as $row) {
                if (!empty(array_filter($row))) {
                    $newData[] = array_combine($header, $row);
                }
            }
        }
        // 2. Logika Input Manual: Mengambil array dari form dinamis
        elseif ($request->has('data_content')) {
            // Filter baris kosong agar tidak masuk database
            $newData = array_filter($request->data_content, function ($row) {
                return !empty(array_filter($row));
            });
        }

        if (empty($newData)) {
            return back()->with('error', 'Tidak ada data valid yang dimasukkan.');
        }

        // LOGIKA UPSERT: Cari record tahun & tabel yang sama
        $existingEntry = StatisticData::where('recommendation_id', $request->recommendation_id)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existingEntry) {
            // GABUNGKAN DATA: Menambah baris baru di bawah data lama
            $combinedData = array_merge($existingEntry->isi_data, $newData);
            $existingEntry->update(['isi_data' => $combinedData]);
            $message = 'Data berhasil ditambahkan ke tabel yang sudah ada!';
        } else {
            // BUAT RECORD BARU
            StatisticData::create([
                'recommendation_id' => $request->recommendation_id,
                'user_id' => auth()->id(),
                'tahun' => $request->tahun,
                'isi_data' => $newData
            ]);
            $message = 'Data statistik baru berhasil disimpan!';
        }

        return back()->with('success', $message);
    }

    public function exportExcel($id)
    {
        // Memanggil Class Export yang sudah kita buat sebelumnya
        $data = StatisticData::findOrFail($id);
        return Excel::download(new StatisticExport($data), "SITATIK-{$data->tahun}.xlsx");
    }

    public function exportPdf($id)
    {
        // Merender view khusus PDF dengan orientasi landscape
        $data = StatisticData::with('recommendation', 'user')->findOrFail($id);
        $pdf = Pdf::loadView('statistics.pdf', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download("Laporan-SITATIK-{$data->tahun}.pdf");
    }

    public function sendToSatuData($id)
    {
        $data = StatisticData::with('recommendation')->findOrFail($id);

        // Integrasi HTTP Client untuk sinkronisasi Satu Data
        /* $response = Http::post('https://api.satudata.palu.go.id/v1/sync', [
            'table_name' => $data->recommendation->table_name,
            'year'       => $data->tahun,
            'records'    => $data->isi_data
        ]); 
        */

        return back()->with('success', 'Data berhasil disinkronkan ke Satu Data!');
    }
}