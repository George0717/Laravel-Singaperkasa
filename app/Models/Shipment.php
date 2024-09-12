<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    // Nama tabel yang sesuai dengan nama tabel di migrasi
    protected $table = 'shipments';

    // Relasi: Shipment dimiliki oleh SalesOrder
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }
}
