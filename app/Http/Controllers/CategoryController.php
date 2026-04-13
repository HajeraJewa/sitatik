<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
public function index(Request $request)
    {
        $query = Category::query();

        if ($request->search) {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%')
                ->orWhere('kode_kategori', 'like', '%' . $request->search . '%');
        }

        $categories = $query->get()->sortBy(function ($item) {
            $parts = collect(explode('.', $item->kode_kategori))
                ->map(fn($num) => (int) $num)
                ->toArray();

            // padding supaya panjang sama
            return array_pad($parts, 5, 0);
    });

        $totalKategori = Category::count();

        return view('kategori.index', compact('categories', 'totalKategori'));
    }

    public function store(Request $request)
    {
        $request->validate(['kode_kategori' => 'required', 'nama_kategori' => 'required']);
        Category::updateOrCreate(['id' => $request->category_id], $request->all());
        return back()->with('success', 'Kategori berhasil diproses!');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return back()->with('success', 'Kategori dihapus!');
    }
}
