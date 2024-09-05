<?php

namespace App\Http\Controllers;

use App\Models\JadwalKirim;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class JadwalKirimController extends Controller
{
    public function index()
    {
        $jadwalKirims = JadwalKirim::with('salesOrder')->get();
        return view('pages.JadwalKirim.index', compact('jadwalKirims'));
    }

    public function create()
    {
        $salesOrders = SalesOrder::all();
        return view('pages.JadwalKirim.create', compact('salesOrders'));
    }

    public function showSalesOrderDetails(Request $request)
    {
        $salesOrderId = $request->input('sales_order_id');
        Log::info('Fetching details for Sales Order ID: ' . $salesOrderId); // Log the request
    
        // Validate that salesOrderId is a valid number
        if (!is_numeric($salesOrderId)) {
            Log::error('Invalid Sales Order ID: ' . $salesOrderId); // Log invalid ID
            return response()->json(['error' => 'Invalid Sales Order ID'], 400);
        }
    
        // Fetch the SalesOrder with its details
        $salesOrder = SalesOrder::with('details')->find($salesOrderId);
    
        if (!$salesOrder) {
            Log::error('Sales Order not found: ' . $salesOrderId); // Log error if not found
            return response()->json(['error' => 'Sales Order not found'], 404);
        }
    
        // Return the Sales Order and its details
        return response()->json([
            'sales_order' => [
                'id' => $salesOrder->id,
                'so_number' => $salesOrder->so_number,
                'customer_name' => $salesOrder->customer_name,
                'customer_address' => $salesOrder->customer_address,
            ],
            'sales_order_details' => $salesOrder->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'item_name' => $detail->item_name,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                ];
            }),
        ]);
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'delivery_date' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        JadwalKirim::create($request->all());

        return redirect()->route('JadwalKirim.index')->with('success', 'Jadwal Kirim berhasil ditambahkan.');
    }

    public function edit(JadwalKirim $jadwalKirim)
    {
        $salesOrders = SalesOrder::all();
        return view('pages.JadwalKirim.edit', compact('jadwalKirim', 'salesOrders'));
    }

    public function update(Request $request, JadwalKirim $jadwalKirim)
    {
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'delivery_date' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $jadwalKirim->update($request->all());

        return redirect()->route('JadwalKirim.index')->with('success', 'Jadwal Kirim berhasil diperbarui.');
    }

    public function show(JadwalKirim $jadwalKirim)
    {
        return view('pages.JadwalKirim.show', compact('jadwalKirim'));
    }

    public function destroy(JadwalKirim $jadwalKirim)
    {
        $jadwalKirim->delete();

        return redirect()->route('JadwalKirim.index')->with('success', 'Jadwal Kirim berhasil dihapus.');
    }

    public function printPDF(JadwalKirim $jadwalKirim)
    {
        $jadwalKirim->load('salesOrder', 'salesOrder.details'); // Load related models if needed
        $pdf = FacadePdf::loadView('pages.JadwalKirim.pdf', compact('jadwalKirim'));
        return $pdf->download('JadwalKirim_' . $jadwalKirim->id . '.pdf');
    }
}
