<?php

namespace App\Http\Controllers;

use App\Models\SalesOrderDetail;
use App\Models\StockHistory;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = SalesOrderDetail::all(); // Ambil semua detail pesanan untuk stok
        return view('stock.index', compact('items'));
    }

    public function show($id)
    {
        $item = SalesOrderDetail::findOrFail($id);
        $histories = StockHistory::where('sales_order_detail_id', $id)->get(); // Ambil riwayat stok untuk item tertentu
        return view('stock.show', compact('item', 'histories'));
    }
}
