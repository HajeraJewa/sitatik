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
            ->with(['statisticData', 'user.perangkatDaerah']);

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
        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
            'tahun' => 'required|integer',
            'excel_file' => 'nullable|mimes:xlsx,xls',
            'data_content' => 'nullable|array'
        ]);

        $newData = [];
        $isExcel = $request->hasFile('excel_file');

        if ($isExcel) {
            $array = Excel::toArray([], $request->file('excel_file'))[0];
            $header = array_shift($array);
            foreach ($array as $row) {
                if (!empty(array_filter($row))) {
                    $newData[] = array_combine($header, $row);
                }
            }
            if (empty($newData)) {
                return back()->with('error', 'File Excel tidak memiliki data valid.');
            }

        } elseif ($request->has('data_content')) {
            $newData = array_values(array_filter($request->data_content, function ($row) {
                return !empty(array_filter($row));
            }));

        }
        $existingEntry = StatisticData::where('recommendation_id', $request->recommendation_id)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existingEntry) {
            if ($isExcel) {
                $oldData = $existingEntry->isi_data;
                foreach ($newData as $newRow) {
                    $keyColumn = array_key_first($newRow);
                    $found = false;
                    foreach ($oldData as $index => $oldRow) {
                        if (trim($oldRow[$keyColumn]) == trim($newRow[$keyColumn])) {
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
                $message = 'Data berhasil disinkronkan dari Excel!';
            } else {
                $finalData = $newData;
                $message = count($newData) > 0 ? 'Perubahan data berhasil disimpan!' : 'Semua data baris telah dihapus!';
            }

            $existingEntry->update(['isi_data' => $finalData]);
        } else {
            if (empty($newData)) {
                return back()->with('error', 'Silakan isi data terlebih dahulu sebelum menyimpan.');
            }

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