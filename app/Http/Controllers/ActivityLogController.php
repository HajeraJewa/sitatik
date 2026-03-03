<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Ambil log terbaru beserta data User dan Perangkat Daerah-nya
        $logs = auth()->user()->role == 'admin'
            ? ActivityLog::with('user.perangkatDaerah')->latest()->paginate(20)
            : ActivityLog::where('user_id', auth()->id())->latest()->paginate(20);

        return view('admin.logs.index', compact('logs'));
    }
}
