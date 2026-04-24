<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Recommendation;
use App\Models\Category;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RecommendationController extends Controller
{
    public function index()
    {
            ? Recommendation::with('user.perangkatDaerah')->latest()->get()
            : Recommendation::where('user_id', auth()->id())->latest()->get();

        $categories = Category::all();

        return view('recommendations.index', compact('recommendations', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string|max:255',
            'table_structure' => 'required',
        ]);

        $rec = Recommendation::create([
            'user_id' => auth()->id(),
            'table_name' => $request->table_name,
            'table_structure' => $request->table_structure,
            'category' => null, // Kategori akan diisi Admin saat approval
            'description' => $request->description,
            'status' => 'pending',
        ]);

        ActivityLog::record(
            'Buat Rekomendasi',
            'Operator mengajukan rekomendasi baru: ' . $rec->table_name
        );

        return back()->with('success', 'Permohonan rekomendasi berhasil dikirim!');
    }

    public function updateStatus(Request $request, $id, $status)
    {
        $rec = Recommendation::findOrFail($id);

        if ($status === 'approved') {
            $request->validate([
                'table_code' => 'required|string|max:50',
                'category' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $rec->update([
                'status' => 'approved',
                'table_code' => strtoupper($request->table_code),
                'category' => $request->category,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'admin_note' => $request->admin_note,
            ]);

            ActivityLog::record(
                'Persetujuan',
                'Admin menyetujui rekomendasi: ' . $rec->table_name . ' dengan Kode: ' . strtoupper($request->table_code)
            );
        } else {
            $rec->update([
                'status' => $status,
                'admin_note' => $request->admin_note,
            ]);

            ActivityLog::record(
                'Update Status',
                'Admin mengubah status ' . $rec->table_name . ' menjadi ' . strtoupper($status)
            );
        }

        return back()->with('success', 'Status permohonan berhasil diperbarui.');
    }

    public function update(Request $request, $id)
    {
        $rec = Recommendation::findOrFail($id);

        if ($rec->user_id !== auth()->id() || $rec->status === 'approved') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'table_name' => 'required|string|max:255',
            'table_structure' => 'required',
        ]);

        $rec->update([
            'table_name' => $request->table_name,
            'table_structure' => $request->table_structure,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        ActivityLog::record(
            'Perbaikan Data',
            'Operator mengirim ulang perbaikan untuk tabel: ' . $rec->table_name
        );

        return back()->with('success', 'Perbaikan berhasil dikirim ulang ke Admin.');
    }

    public function exportPdf($id)
    {
        $rec = Recommendation::with('user.perangkatDaerah')->findOrFail($id);

        $data = [
            'title' => 'Lembar Rekomendasi Struktur Tabel',
            'date' => date('d/m/Y'),
            'rec' => $rec,
            'columns' => explode(',', $rec->table_structure)
        ];
        ActivityLog::record('Cetak PDF', 'Mendownload dokumen PDF rekomendasi: ' . $rec->table_name);

        $pdf = Pdf::loadView('recommendations.pdf', $data);

        return $pdf->download('rekomendasi-' . str_replace(' ', '-', $rec->table_name) . '.pdf');
    }
}