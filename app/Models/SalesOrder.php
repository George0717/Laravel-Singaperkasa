<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'customer_address', 'po_date', 'po_number', 'so_number',
        'discount', 'down_payment', 'vat', 'grand_total', 'payment_schedule_type'
    ];

    public function details()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }
}
