<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    use HasFactory;

    // Nama tabel yang sesuai dengan nama tabel di migrasi
    protected $table = 'sales_order_details';

    // Daftar atribut yang dapat diisi secara massal
    protected $fillable = [
        'sales_order_id',
        'item_name',
        'quantity',
        'price',
        'total',
        'delivered_quantity'
    ];

    // Daftar atribut yang harus di-cast ke tipe data tertentu
    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relasi: SalesOrderDetail milik SalesOrder
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function details()
    {
        return $this->hasMany(SalesOrderDetail::class, 'sales_order_id');
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->delivered_quantity;
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'sales_order_id');
    }

}
