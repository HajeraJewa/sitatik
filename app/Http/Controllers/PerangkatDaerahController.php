<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;

class PerangkatDaerahController extends Controller
{
  public function index()
  {
    $opds = PerangkatDaerah::latest()->get();
    return view('perangkat_daerah.index', compact('opds'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'kode_opd' => 'required|unique:perangkat_daerah,kode_opd,' . $request->id,
      'nama_opd' => 'required',
    ]);

    // FIX 1: Tentukan tipe aksi berdasarkan ada tidaknya ID
    $actionType = $request->id ? 'Update' : 'Tambah';

    // FIX 2: Simpan ke dalam variabel agar data bisa dipanggil untuk log
    $opd = PerangkatDaerah::updateOrCreate(
      ['id' => $request->id],
      $request->only('kode_opd', 'nama_opd', 'alias_opd')
    );

    // FIX 3: Gunakan variabel yang sudah didefinisikan
    ActivityLog::record(
      $actionType . ' Perangkat Daerah',
      "Admin melakukan $actionType data instansi: " . $opd->nama_opd . " (" . $opd->kode_opd . ")"
    );

    return back()->with('success', 'Data instansi berhasil disimpan!');
  }

  public function destroy($id)
  {
    $opd = PerangkatDaerah::findOrFail($id);

    // FIX 4: Catat log sebelum data benar-benar dihapus
    ActivityLog::record(
      'Hapus Perangkat Daerah',
      "Admin menghapus data instansi: " . $opd->nama_opd
    );

    $opd->delete();

    return back()->with('success', 'Data instansi berhasil dihapus!');
  }
}