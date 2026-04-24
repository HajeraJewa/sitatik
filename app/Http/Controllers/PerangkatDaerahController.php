<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;

class PerangkatDaerahController extends Controller
{
  public function index()
  {
    $opds = PerangkatDaerah::orderBy('kode_opd', 'asc')->get();
    return view('perangkat_daerah.index', compact('opds'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'kode_opd' => 'required|unique:perangkat_daerah,kode_opd,' . $request->id,
      'nama_opd' => 'required',
    ]);

    $actionType = $request->id ? 'Update' : 'Tambah';

    $opd = PerangkatDaerah::updateOrCreate(
      ['id' => $request->id],
      $request->only('kode_opd', 'nama_opd', 'alias_opd')
    );

    ActivityLog::record(
      $actionType . ' Perangkat Daerah',
      "Admin melakukan $actionType data instansi: " . $opd->nama_opd . " (" . $opd->kode_opd . ")"
    );

    return back()->with('success', 'Data instansi berhasil disimpan!');
  }

  public function destroy($id)
  {
    $opd = PerangkatDaerah::findOrFail($id);

    ActivityLog::record(
      'Hapus Perangkat Daerah',
      "Admin menghapus data instansi: " . $opd->nama_opd
    );

    $opd->delete();

    return back()->with('success', 'Data instansi berhasil dihapus!');
  }
}