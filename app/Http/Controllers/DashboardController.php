<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Recommendation;
use App\Models\Category;
use App\Models\StatisticData;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $opd_id = $request->opd_id;

        $query = User::where('role', 'operator')
            ->with([
                'perangkatDaerah',
                'statisticData' => function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                }
            ]);

        if ($opd_id) {
            $query->where('id', $opd_id);
        }

        $opds = $query->get();

        $opd_selesai = 0;
        $opd_proses = 0;
        $opd_belum = 0;

        foreach ($opds as $opd) {
            $tabel_details = [];
            $recommendations = \App\Models\Recommendation::where('user_id', $opd->id)->get();
            $total_tabel = $recommendations->count();
            
            $count_fixed = 0;
            $count_draft = 0;
            $nilai_progres = 0;

            foreach ($recommendations as $rec) {
                $statData = $opd->statisticData->where('recommendation_id', $rec->id)->first();
                $status = 'Belum';

                if ($statData) {
                    if ($statData->is_final) {
                        $status = 'Selesai';
                        $count_fixed++;
                        $nilai_progres += 1;
                    } else {
                        $status = 'Proses';
                        $count_draft++;
                        $nilai_progres += 0.5;
                    }
                }

                $tabel_details[] = [
                    'table_name' => $rec->table_name,
                    'status' => $status
                ];
            }

            $opd->tabel_details = $tabel_details;

            if ($total_tabel > 0) {
                $opd->persentase = round(($nilai_progres / $total_tabel) * 100);
                
                
                if ($count_fixed == $total_tabel) {
                    $opd->status_label = 'Selesai';
                    $opd_selesai++;
                } elseif ($nilai_progres > 0) {
                    $opd->status_label = 'Proses';
                    $opd_proses++;
                } else {
                    $opd->status_label = 'Belum';
                    $opd_belum++;
                }
            } else {
                $opd->persentase = 0;
                $opd->status_label = 'Belum';
                $opd_belum++;
            }
        }

        $opds_map = $opds->whereNotNull('latitude')->whereNotNull('longitude')->values();
        $total_rekomendasi = Recommendation::count();
        $tabel_aktif = StatisticData::where('tahun', $tahun)->count();
        $total_kategori = Category::count();
        $rata_progres = $opds->count() > 0 ? round($opds->avg('persentase'), 1) : 0;

        return view('dashboard', compact(
            'opds', 'opds_map', 'total_rekomendasi', 'total_kategori', 
            'tabel_aktif', 'opd_selesai', 'opd_proses', 'opd_belum', 
            'rata_progres', 'tahun'
        ));
        
    }

    public function filterOpd(Request $request)
    {
        $opd_id = $request->opd_id;
        $tahun = $request->tahun ?? date('Y');

        $query = User::where('role', 'operator')->with(['perangkatDaerah', 'statisticData' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }]);

        if ($opd_id) $query->where('id', $opd_id);

        $opds = $query->get();
        $result = [];

        foreach ($opds as $opd) {
            $recs = \App\Models\Recommendation::where('user_id', $opd->id)->get();
            $total = $recs->count();
            $fixed = 0; $draft = 0; $bobot = 0;
            $details = [];

            foreach ($recs as $rec) {
                $stat = $opd->statisticData->where('recommendation_id', $rec->id)->first();
                $st = 'Belum';
                if ($stat) {
                    if ($stat->is_final) { $st = 'Selesai'; $fixed++; $bobot += 1; }
                    else { $st = 'Proses'; $draft++; $bobot += 0.5; }
                }
                $details[] = ['table_name' => $rec->table_name, 'status' => $st];
            }

            $result[] = [
                'id' => $opd->id,
                'nama' => $opd->perangkatDaerah->nama_opd ?? $opd->name,
                'perangkat_daerah' => $opd->perangkatDaerah, // 🔥 TAMBAHKAN INI
                'persentase' => $total > 0 ? round(($bobot / $total) * 100) : 0,
                'tabel_details' => $details,
                'latitude' => $opd->latitude,
                'longitude' => $opd->longitude,
                'email' => $opd->email
            ];
        }
        return response()->json($result);
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        \App\Models\ActivityLog::record(
            'Hapus Pengguna',
            'Admin menghapus akun operator: ' . $user->email . ' (' . ($user->perangkatDaerah->nama_opd ?? $user->name) . ')'
        );

        $user->delete();

        return back()->with('success', 'Operator berhasil dihapus.');
    }
}