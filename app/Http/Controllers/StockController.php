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
        $salesOrders = SalesOrderDetail::findOrFail($id);
        $histories = StockHistory::where('sales_order_detail_id', $id)->get(); // Ambil riwayat stok untuk item tertentu
        return view('pages.StockBarangSO.show', compact('salesOrders', 'histories'));
    }
}
