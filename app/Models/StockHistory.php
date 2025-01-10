<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'item_name',
        'change_quantity',
        'reason',
        'date',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}

