<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id', 'invoice_number', 'subtotal', 'discount', 'down_payment', 'vat', 'grand_total', 'payment_schedule_type'
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    // Add methods to calculate totals, etc.
}
