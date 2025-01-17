<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBarang extends Model
{
    use HasFactory;
    protected $fillable = ['nama_barang', 'jumlah_barang', 'tipe_barang'];
    protected $table = 'stock_barang';

    public function salesOrders()
    {
        return $this->belongsToMany(SalesOrder::class, 'sales_order_items')->withPivot('jumlah');
    }

    public function salesOrderDetails()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }

    // Method untuk mengurangi stock
    public function decreaseStock(int $quantity)
    {
        $this->jumlah_barang -= $quantity;
        $this->save();
    }
}
