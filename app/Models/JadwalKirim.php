<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKirim extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'delivery_date',
        'keterangan',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
