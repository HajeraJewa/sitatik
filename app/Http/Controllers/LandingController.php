<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    public function index()
    {
        // 🔥 Redirect kalau sudah login
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // dummy data (nanti bisa dari DB)
        $statistik = [
            'kategori' => 22,
            'data' => 87583,
            'pengguna' => 108,
            'pengunjung' => 1261,
        ];

        $kategori = [
            'Perkebunan',
            'Keadaan Geografi',
            'Data Sektoral',
            'Matriks Data',
            'Agama dan Sosial Lainnya',
            'Perikanan'
        ];

        return view('landing.index', compact('statistik', 'kategori'));
    }
}