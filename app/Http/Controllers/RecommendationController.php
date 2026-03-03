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
        // Admin melihat semua dengan relasi user & OPD, Operator melihat miliknya sendiri
        $recommendations = auth()->user()->role == 'admin'
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

        // Pencatatan Log Aktivitas
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
            // PERBAIKAN: Tambahkan table_code ke dalam validasi agar bisa diproses
            $request->validate([
                'table_code' => 'required|string|max:50', // Tambahkan baris ini
                'category' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $rec->update([
                'status' => 'approved',
                'table_code' => strtoupper($request->table_code), // Sekarang ini akan berfungsi
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
            // Logika untuk status ditolak/dikoreksi tetap sama
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

        // Proteksi akses operator
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
            'status' => 'pending', // Kembali ke pending untuk dicek Admin
        ]);

        ActivityLog::record(
            'Perbaikan Data',
            'Operator mengirim ulang perbaikan untuk tabel: ' . $rec->table_name
        );

        return back()->with('success', 'Perbaikan berhasil dikirim ulang ke Admin.');
    }

    public function exportPdf($id)
    {
        // Eager load perangkatDaerah untuk data instansi di PDF
        $rec = Recommendation::with('user.perangkatDaerah')->findOrFail($id);

        $data = [
            'title' => 'Lembar Rekomendasi Struktur Tabel',
            'date' => date('d/m/Y'),
            'rec' => $rec,
            'columns' => explode(',', $rec->table_structure)
        ];

        // Catat log cetak
        ActivityLog::record('Cetak PDF', 'Mendownload dokumen PDF rekomendasi: ' . $rec->table_name);

        $pdf = Pdf::loadView('recommendations.pdf', $data);

        return $pdf->download('rekomendasi-' . str_replace(' ', '-', $rec->table_name) . '.pdf');
    }
}