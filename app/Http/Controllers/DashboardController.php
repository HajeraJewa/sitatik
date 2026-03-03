<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Recommendation;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Filter hanya pengguna dengan role 'operator'
        // Akun admin tidak akan ditarik ke dalam koleksi ini
        $opds = User::where('role', 'operator')
            ->with(['perangkatDaerah', 'recommendations'])
            ->get();

        $opd_selesai = 0;
        $opd_proses = 0;
        $opd_belum = 0;

        // 2. Kalkulasi statistik progres hanya untuk operator
        foreach ($opds as $opd) {
            $total = $opd->recommendations->count();
            $approved = $opd->recommendations->where('status', 'approved')->count();

            // Persentase dinamis per instansi
            $opd->persentase = $total > 0 ? round(($approved / $total) * 100) : 0;

            // Klasifikasi status untuk monitoring
            if ($total > 0 && $total == $approved) {
                $opd_selesai++;
            } elseif ($total > 0 && $approved < $total) {
                $opd_proses++;
            } else {
                $opd_belum++;
            }
        }

        // 3. Filter khusus untuk marker peta (Hanya operator yang memiliki koordinat)
        $opds_map = $opds->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->values();

        // 4. Ringkasan Statistik Global berdasarkan data operator
        $total_rekomendasi = Recommendation::count();
        $total_kategori = Category::count();
        $tabel_aktif = Recommendation::where('status', 'approved')->count();
        $rata_progres = $opds->count() > 0 ? round($opds->avg('persentase'), 1) : 0;

        // Mengirimkan variabel ke View
        return view('dashboard', compact(
            'opds',
            'opds_map',
            'total_rekomendasi',
            'total_kategori',
            'tabel_aktif',
            'opd_selesai',
            'opd_proses',
            'opd_belum',
            'rata_progres'
        ));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Mencatat penghapusan ke Log Aktivitas sebelum data dihapus
        \App\Models\ActivityLog::record(
            'Hapus Pengguna',
            'Admin menghapus akun operator: ' . $user->email . ' (' . ($user->perangkatDaerah->nama_opd ?? $user->name) . ')'
        );

        $user->delete();

        return back()->with('success', 'Operator berhasil dihapus.');
    }
}