<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PerangkatDaerah; // Tambahkan import Model ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Mengambil user dengan relasi perangkatDaerah agar nama instansi muncul
        $users = User::with('perangkatDaerah')->where('role', 'operator')->latest()->get();

        // FIX: Ambil semua data instansi untuk dropdown di View (Atasi error image_cade41)
        $all_opd = PerangkatDaerah::all();

        return view('admin.users.index', compact('users', 'all_opd'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id', // Validasi ID OPD
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Simpan User dengan menghubungkan ID Perangkat Daerah
        User::create([
            'perangkat_daerah_id' => $request->perangkat_daerah_id, // Hubungkan ke Master OPD
            'name' => 'Operator OPD', // Nama akan otomatis ditarik dari relasi OPD di View
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'operator',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return back()->with('success', 'Operator OPD berhasil ditambahkan dan dihubungkan!');
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $request->validate([
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8', // Password dibuat opsional (boleh kosong)
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $data = [
            'perangkat_daerah_id' => $request->perangkat_daerah_id,
            'email' => $request->email,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        // LOGIKA UTAMA: Hanya update password jika kolom diisi oleh Admin
        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        \App\Models\ActivityLog::record(
            'Update Pengguna',
            'Admin memperbarui profil/password operator: ' . $user->email
        );

        return redirect()->route('users.index')->with('success', 'Data operator dan password berhasil diperbarui!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun operator berhasil dihapus!');
    }
}