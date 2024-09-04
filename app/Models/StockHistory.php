<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_detail_id',
        'change_quantity',
        'reason',
        'date',
    ];

    public function sales_order()
    {
        return $this->belongsTo(SalesOrderDetail::class, 'sales_order_detail_id');
    }
}
