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
        $query = Recommendation::where('status', 'approved')
            ->with(['statisticData', 'user.perangkatDaerah', 'category']);

        // Filter OPD
        if ($request->filled('opd_id')) {
            $query->where('user_id', $request->opd_id);
        }

        // Filter Tahun
        if ($request->filled('tahun')) {
            $query->whereYear('start_date', $request->tahun);
        }

        // Filter Search
        if ($request->filled('search')) {
            $query->where('table_name', 'like', '%' . $request->search . '%');
        }

        // Logic Role
        if (auth()->user()->role == 'admin') {
            $allOpd = User::where('role', 'operator')->with('perangkatDaerah')->get();
        } else {
            $query->where('user_id', auth()->id());
            $allOpd = collect();
        }

        $statistics = $query->latest()->get();

        $approvedTables = Recommendation::where('status', 'approved')
            ->where('user_id', auth()->id())
            ->get();

        return view('statistics.index', compact('statistics', 'approvedTables', 'allOpd'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Dasar
        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
            'tahun' => 'required|integer',
            'excel_file' => 'nullable|mimes:xlsx,xls|max:10240', // Max 10MB
            'data_content' => 'nullable|array'
        ]);

        // Ambil data rekomendasi untuk mengecek struktur tabel yang seharusnya
        $rec = Recommendation::findOrFail($request->recommendation_id);
        // Mengubah string struktur (kolom1, kolom2) menjadi array dan di-trim spasinga
        $expectedColumns = array_map('trim', explode(',', $rec->table_structure));

        $newData = [];
        $isExcel = $request->hasFile('excel_file');

        try {
            if ($isExcel) {
                // 2. Proses Excel
                $array = Excel::toArray([], $request->file('excel_file'))[0];
                $header = array_map('trim', array_shift($array)); // Ambil header dan bersihkan spasi

                // --- VALIDASI STRUKTUR KOLOM ---
                // Cek apakah kolom di Excel cocok dengan kolom di database
                $missingColumns = array_diff($expectedColumns, $header);
                if (!empty($missingColumns)) {
                    return back()->with('error', 'Struktur Excel salah. Kolom berikut tidak ditemukan: ' . implode(', ', $missingColumns));
                }

                foreach ($array as $row) {
                    // Pastikan baris tidak kosong sama sekali
                    if (!empty(array_filter($row))) {
                        // Gabungkan header dengan baris, hanya ambil kolom yang terdaftar di expectedColumns
                        $rowCombined = array_combine($header, $row);
                        $newData[] = array_intersect_key($rowCombined, array_flip($expectedColumns));
                    }
                }

                if (empty($newData)) {
                    return back()->with('error', 'File Excel tidak memiliki data yang bisa diproses.');
                }

            } elseif ($request->has('data_content')) {
                // 3. Proses Input Manual
                $newData = array_values(array_filter($request->data_content, function ($row) {
                    return !empty(array_filter($row));
                }));
            }

            // Cek data yang sudah ada untuk tahun dan rekomendasi tersebut
            $existingEntry = StatisticData::where('recommendation_id', $request->recommendation_id)
                ->where('tahun', $request->tahun)
                ->first();

            if ($existingEntry) {
                if ($isExcel) {
                    // Sinkronisasi: Update jika kunci sama, tambah jika baru
                    $oldData = $existingEntry->isi_data;
                    foreach ($newData as $newRow) {
                        $keyColumn = array_key_first($newRow);
                        $found = false;
                        foreach ($oldData as $index => $oldRow) {
                            if (isset($oldRow[$keyColumn]) && trim($oldRow[$keyColumn]) == trim($newRow[$keyColumn])) {
                                $oldData[$index] = $newRow;
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $oldData[] = $newRow;
                        }
                    }
                    $finalData = $oldData;
                    $message = 'Data Excel berhasil disinkronkan ke data lama!';
                } else {
                    // Input manual menimpa data lama (sesuai UI Anda)
                    $finalData = $newData;
                    $message = count($newData) > 0 ? 'Perubahan data berhasil disimpan!' : 'Data telah dibersihkan!';
                }

                $existingEntry->update(['isi_data' => $finalData]);
            } else {
                // Simpan Data Baru
                if (empty($newData)) {
                    return back()->with('error', 'Silakan masukkan data terlebih dahulu.');
                }

                StatisticData::create([
                    'recommendation_id' => $request->recommendation_id,
                    'user_id' => auth()->id(),
                    'tahun' => $request->tahun,
                    'isi_data' => $newData
                ]);
                $message = 'Data statistik berhasil disimpan!';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            // Menangkap error tak terduga (misal: jumlah kolom tidak pas saat array_combine)
            return back()->with('error', 'Gagal memproses data: Pastikan format file benar.');
        }
    }
    public function destroyData($id)
    {
        $data = StatisticData::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $data->user_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus data ini.');
        }

        $data->delete();

        return back()->with('success', 'Seluruh isi data tabel berhasil dihapus.');
    }
    public function exportExcel($id)
    {
        $data = StatisticData::findOrFail($id);
        return Excel::download(new StatisticExport($data), "SITATIK-{$data->tahun}.xlsx");
    }

    public function exportPdf($id)
    {
        $data = StatisticData::with('recommendation', 'user')->findOrFail($id);
        $pdf = Pdf::loadView('statistics.pdf', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download("Laporan-SITATIK-{$data->tahun}.pdf");
    }

    public function sendToSatuData($id)
    {
        $data = StatisticData::with('recommendation')->findOrFail($id);

        return back()->with('success', 'Data berhasil disinkronkan ke Satu Data!');
    }
    public function finalize($id)
    {
        $data = StatisticData::findOrFail($id);

        if ($data->user_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengunci data ini.');
        }

        $data->update(['is_final' => true]);

        return back()->with('success', 'Data berhasil dikunci (FIXED). Data tidak dapat diubah lagi.');
    }
}