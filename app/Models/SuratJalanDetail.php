<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_jalan_id',
        'sales_order_detail_id',
        'quantity',
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }

    public function salesOrderDetail()
    {
        return $this->belongsTo(SalesOrderDetail::class);
    }
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'sales_order_id');
    }
}
