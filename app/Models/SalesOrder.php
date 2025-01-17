<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel yang sesuai dengan nama tabel di migrasi
    protected $table = 'sales_orders';

    // Daftar atribut yang dapat diisi secara massal
    protected $fillable = [
        'customer_name',
        'discount',
        'down_payment',
        'vat',
        'grand_total',
        'payment_schedule_type',
        'payment_type',
        'due_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'stock_barang_id',
    ];

    // Daftar atribut yang harus di-cast ke tipe data tertentu
    protected $casts = [
        'due_date' => 'date',
        'discount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'vat' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    // Relasi: SalesOrder memiliki banyak SalesOrderDetail
    public function details()
    {
        return $this->hasMany(SalesOrderDetail::class, 'sales_order_id');
    }

    // Relasi: SalesOrder dimiliki oleh User
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'sales_order_id');
    }

    public function jadwalKirim()
    {
        return $this->hasOne(JadwalKirim::class);
    }
    public function suratJalans()
    {
        return $this->hasMany(SuratJalan::class);
    }

    public function salesOrderDetail()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }

    public function stockBarangs()
    {
        return $this->belongsToMany(StockBarang::class, 'sales_order_items')->withPivot('jumlah');
    }

 }

