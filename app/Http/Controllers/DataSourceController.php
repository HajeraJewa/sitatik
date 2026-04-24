<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Http\Request;

class DataSourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Recommendation::where('status', 'approved')
            ->with(['user.perangkatDaerah', 'category']);

        // 2. Logika Filter OPD
        if ($request->filled('opd_id')) {
            $query->where('user_id', $request->opd_id);
        }

        if ($request->filled('table_name')) {

            $query->where('table_name', 'like', '%' . $request->table_name . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        $sources = $query->latest()->get();

        $allOpd = User::where('role', 'operator')->with('perangkatDaerah')->get();

        $allCategories = \App\Models\Category::all();

        $listTables = Recommendation::where('status', 'approved')
            ->select('table_name')
            ->distinct()
            ->get();

        return view('sumber.index', compact('sources', 'allOpd', 'allCategories', 'listTables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
            'nama_sumber' => 'required|string|max:255',
        ]);

        $referensi = Recommendation::findOrFail($request->recommendation_id);

        $newSource = new Recommendation();

        $newSource->table_name = $referensi->table_name;
        $newSource->user_id = $referensi->user_id;
        $newSource->table_structure = $referensi->table_structure;

        $newSource->category_id = $referensi->category_id;

        $newSource->category = $referensi->category;
        // -------------------------

        $newSource->status = 'approved';

        $newSource->data_source_name = $request->nama_sumber;

        $newSource->save();

        return redirect()->back()->with('success', 'Data baru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $source = Recommendation::findOrFail($id);
        $source->delete();

        return redirect()->back()->with('success', 'Data sumber berhasil dihapus!');
    }
}