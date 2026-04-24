<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PerangkatDaerah;
use App\Models\Recommendation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Exports\SitatikExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        $type = $request->type;
        $format = $request->format;
        $data = [];
        $view = '';

        if ($type == 'pengguna') {
            $data = User::with('perangkatDaerah')->where('role', 'operator')->get();
            $view = 'reports.pdf_pengguna';
        } elseif ($type == 'opd') {
            $data = PerangkatDaerah::all();
            $view = 'reports.pdf_opd';
        } elseif ($type == 'sumber_data') {
            $data = Recommendation::with('perangkatDaerah')->where('status', 'approved')->get();
            $view = 'reports.pdf_sumber_data';
        } elseif ($type == 'rekomendasi') {
            $data = Recommendation::with('perangkatDaerah')->latest()->get();
            $view = 'reports.pdf_rekomendasi';
        }

        if ($format == 'excel') {
            return Excel::download(new SitatikExport($data, $type), 'Laporan_' . $type . '_' . now()->format('dmy') . '.xlsx');
        }
        $pdf = Pdf::loadView($view, compact('data'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_' . $type . '_' . now()->format('dmy') . '.pdf');
    }
}