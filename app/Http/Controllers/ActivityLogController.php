<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
{
    $logs = \App\Models\ActivityLog::with('user')->latest()->paginate(20);

    return view('superAdmin.riwayat.index', compact('logs'));
}

public function show($id)
{
    $log = ActivityLog::findOrFail($id);

    return view('superAdmin.riwayat.show', compact('log'));
}

}
