<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id', 'item_name', 'quantity', 'price', 'total'
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
