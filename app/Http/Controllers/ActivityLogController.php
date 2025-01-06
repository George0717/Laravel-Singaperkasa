<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Mengambil semua aktivitas
        $activities = Activity::orderBy('created_at', 'desc')->get();

        return view('pages.riwayat.index', compact('activities'));
    }
}
