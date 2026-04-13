<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Recommendation;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $opd_id = $request->opd_id;

        // QUERY USER OPD + FILTER TAHUN DI RELASI
        $query = User::where('role', 'operator')
            ->with(['perangkatDaerah', 'recommendations' => function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun);
            }]);

        if ($opd_id) {
            $query->where('id', $opd_id);
        }

        $opds = $query->get();

        $opd_selesai = 0;
        $opd_proses = 0;
        $opd_belum = 0;

        foreach ($opds as $opd) {
            $total = $opd->recommendations->count();
            $approved = $opd->recommendations->where('status', 'approved')->count();

            $opd->persentase = $total > 0 ? round(($approved / $total) * 100) : 0;

            if ($total > 0 && $total == $approved) {
                $opd_selesai++;
            } elseif ($total > 0 && $approved < $total) {
                $opd_proses++;
            } else {
                $opd_belum++;
            }
        }

        $opds_map = $opds->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->values();

        // STATISTIK IKUT FILTER
        $rekomQuery = Recommendation::whereYear('created_at', $tahun);

        if ($opd_id) {
            $rekomQuery->where('user_id', $opd_id);
        }

        $total_rekomendasi = $rekomQuery->count();

        $tabel_aktif = Recommendation::where('status', 'approved')
            ->whereYear('created_at', $tahun)
            ->when($opd_id, fn($q) => $q->where('user_id', $opd_id))
            ->count();

        $total_kategori = Category::count();

        $rata_progres = $opds->count() > 0 ? round($opds->avg('persentase'), 1) : 0;

        return view('dashboard', compact(
            'opds',
            'opds_map',
            'total_rekomendasi',
            'total_kategori',
            'tabel_aktif',
            'opd_selesai',
            'opd_proses',
            'opd_belum',
            'rata_progres',
            'tahun'
        ));
    }

    public function filterOpd(Request $request)
    {
        $opd_id = $request->opd_id;

        $query = User::where('role', 'operator')
            ->with(['perangkatDaerah', 'recommendations']);

        if ($opd_id) {
            $query->where('id', $opd_id);
        }

        $opds = $query->get();

        $result = [];

        foreach ($opds as $opd) {
            $total = $opd->recommendations->count();
            $approved = $opd->recommendations->where('status', 'approved')->count();

            $persentase = $total > 0 ? round(($approved / $total) * 100) : 0;

            $result[] = [
                'id' => $opd->id,
                'nama' => $opd->perangkatDaerah->nama_opd ?? $opd->name,
                'persentase' => $persentase,
                'latitude' => $opd->latitude,
                'longitude' => $opd->longitude,
            ];
        }

        return response()->json($result);
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
