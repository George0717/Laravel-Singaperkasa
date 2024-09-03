<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'plat_angkutan',
        'tanggal_pengiriman',
        'no_surat_jalan',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function suratJalanDetails()
    {
        return $this->hasMany(SuratJalanDetail::class);
    }
}



