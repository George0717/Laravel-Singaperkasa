<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\SalesOrder;
use App\Observers\SalesOrderObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SalesOrder::observe(SalesOrderObserver::class);

        // Menggunakan View Composer untuk berbagi notifikasi dengan semua tampilan
        View::composer('components.app.header', function ($view) {
            // Mengambil data notifikasi dari ActivityLog
            $notifications = ActivityLog::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'title' => ($log->user && $log->user->name ? $log->user->name : 'Sistem') . " melakukan {$log->action}",
                        'message' => $log->description,
                        'date' => $log->created_at->diffForHumans(),
                    ];
                });

            // Mendistribusikan data notifikasi ke view
            $view->with('notifications', $notifications);
        });

        View::composer('layouts.superAdmin', function ($view) {
            // Mengambil data notifikasi dari ActivityLog
            $notifications = ActivityLog::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'title' => ($log->user && $log->user->name ? $log->user->name : 'Sistem') . " melakukan {$log->action}",
                        'message' => $log->description,
                        'date' => $log->created_at->diffForHumans(),
                    ];
                });

            // Mendistribusikan data notifikasi ke view
            $view->with('notifications', $notifications);
        });
    }
}
