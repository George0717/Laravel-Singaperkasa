<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with(['user'])
            ->latest()
            ->paginate(20);

        // Menyiapkan notifikasi
        $notifications = ActivityLog::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'title' => ($log->user && $log->user->name ? $log->user->name : 'Sistem') . " melakukan {$log->log_name}",
                    'message' => $log->description . ($log->properties['so_number'] ?? ''),
                    'date' => $log->created_at->diffForHumans(),
                ];
            });

        return view('admin.riwayat.index', compact('logs', 'notifications'));
    }


    public function show($id)
    {
        $log = ActivityLog::findOrFail($id);

        // Debugging: cek apakah data ada dalam kolom 'changes'

        // Ambil model berdasarkan model_type dan model_id
        $modelClass = $log->model_type;
        $model = null;

        if (class_exists($modelClass)) {
            $model = $modelClass::withTrashed()->find($log->model_id);
        }

        // Daftar kolom yang diizinkan untuk setiap model
        $allowedColumns = [
            'App\Models\SalesOrder' => [
                'customer_name',
                'nama_sales',
                'so_number',
                'discount',
                'discount_type',
                'payment_type',
                'grand_total',
                'deleted_at',
            ],
            'App\Models\JadwalKirim' => [
                'delivery_date',
                'keterangan',
                'tujuan_pengiriman',
            ],
            'App\Models\SuratJalan' => [
                'plat_angkutan',
                'tanggal_pengiriman',
                'no_surat_jalan',
            ],
            'App\Models\Invoice' => [
                'invoice_number',
                'discount',
                'down_payment',
                'vat',
                'grand_total',
                'payment_type'
            ],
        ];

        // Siapkan data lama dan data baru (jika tersedia)
        $dataLama = $log->changes['old'] ?? null;
        $dataBaru = $log->changes['new'] ?? null;

        // Filter data sesuai kolom yang diizinkan
        $filteredDataLama = $dataLama ? array_filter(
            $dataLama,
            fn($key) => in_array($key, $allowedColumns[$log->model_type] ?? []),
            ARRAY_FILTER_USE_KEY
        ) : null;

        $filteredDataBaru = $dataBaru ? array_filter(
            $dataBaru,
            fn($key) => in_array($key, $allowedColumns[$log->model_type] ?? []),
            ARRAY_FILTER_USE_KEY
        ) : null;
        $changes = [
            'old' => $model->getOriginal(),
            'new' => $model->getAttributes(),
        ];        // Return ke view dengan data yang difilter

        return view('admin.riwayat.show', [
            'log' => $log,
            'model' => $model,
            'dataLama' => $filteredDataLama,
            'dataBaru' => $filteredDataBaru,
            'action' => $log->action,
            'changes' => $changes,
        ]);
    }


    public function restore($id)
    {
        $log = ActivityLog::findOrFail($id);

        // Periksa apakah action adalah 'deleted'
        if ($log->action === 'deleted') {
            $modelClass = $log->model_type;

            if (class_exists($modelClass)) {
                $model = $modelClass::withTrashed()->find($log->model_id);

                if ($model && $model->trashed()) {
                    // Cek apakah model sudah dipulihkan sebelumnya
                    if ($model->restored_at) {
                        return redirect()->route('admin.riwayat.index')->with('error', 'Data sudah dipulihkan sebelumnya.');
                    }

                    $model->restore(); // Mengembalikan data yang dihapus
                    $model->restored_at = now(); // Tandai waktu pemulihan
                    $model->save(); // Simpan waktu pemulihan
                    return redirect()->route('admin.riwayat.index')->with('success', 'Data berhasil dikembalikan.');
                }
            }
        }

        // Logika untuk 'updated' action
        elseif ($log->action === 'updated') {
            $modelClass = $log->model_type;

            if (class_exists($modelClass)) {
                $model = $modelClass::find($log->model_id);

                if ($model) {
                    // Kembalikan ke data lama
                    $oldData = $log->changes['old'] ?? [];
                    DB::table($model->getTable())
                        ->where('id', $model->id)
                        ->update($oldData);

                    return redirect()->route('admin.riwayat.index')->with('success', 'Data berhasil dikembalikan ke versi lama.');
                }
            }
        }

        return redirect()->route('admin.riwayat.index')->with('error', 'Restore data gagal.');
    }




    public function user()
    {
        return $this->belongsTo(User::class, 'causer_id'); // Sesuaikan nama kolom jika berbeda
    }
}
