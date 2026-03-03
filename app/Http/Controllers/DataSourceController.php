<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Http\Request;

class DataSourceController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data tabel yang sudah disetujui
        $query = Recommendation::where('status', 'approved')->with('user');

        // Fitur Filter berdasarkan OPD
        if ($request->opd_id) {
            $query->where('user_id', $request->opd_id);
        }

        $sources = $query->get();
        // Memastikan model User terdeteksi untuk dropdown filter
        $allOpd = User::where('role', 'operator')->get();

        return view('sumber.index', compact('sources', 'allOpd'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
            'nama_sumber' => 'required|string|max:255',
        ]);

        $referensi = Recommendation::findOrFail($request->recommendation_id);

        // Menambah data baru sebagai baris baru di tabel
        $newSource = new Recommendation();

        // Copy data wajib dari referensi agar tidak error
        $newSource->table_name = $referensi->table_name;
        $newSource->user_id = $referensi->user_id;
        $newSource->table_structure = $referensi->table_structure;
        $newSource->category = $referensi->category;
        $newSource->status = 'approved';

        // Isi dengan nama sumber yang baru diinput
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