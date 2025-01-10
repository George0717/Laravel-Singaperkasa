<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua Sales Order beserta detailnya
        $salesOrders = SalesOrder::with('details')->get();

        // Pastikan bahwa $salesOrders mengandung data
        if ($salesOrders->isEmpty()) {
            // Jika kosong, tambahkan log atau pesan debugging
            Log::info('Tidak ada data sales orders yang ditemukan');
        }

        // Kirim data ke view
        return view('pages.StockBarangSO.index', compact('salesOrders'));
    }

    public function show($id)
    {
        // Ambil detail dari StockHistory berdasarkan ID
        $stockHistory = StockHistory::with('salesOrder')->findOrFail($id);

        // Tampilkan view dengan data StockHistory
        return view('pages.StockBarangSO.show', compact('stock_histories'));
    }
}
