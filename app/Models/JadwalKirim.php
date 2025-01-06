<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JadwalKirim extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'sales_order_id',
        'delivery_date',
        'keterangan',
        'tujuan_pengiriman',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Jadwal Kirim telah {$eventName}")
            ->logOnly(['sales_order_id', 'delivery_date', 'keterangan', 'tujuan_pengiriman']);
    }
}
