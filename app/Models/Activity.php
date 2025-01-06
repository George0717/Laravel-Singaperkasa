<?php
use Spatie\Activitylog\Models\Activity;

// Mengambil semua aktivitas
$activities = Activity::orderBy('created_at', 'desc')->get();