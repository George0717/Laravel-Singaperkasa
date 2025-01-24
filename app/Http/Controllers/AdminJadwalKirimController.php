<?php

namespace App\Http\Controllers;

use App\Models\JadwalKirim;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class AdminJadwalKirimController extends Controller
{
    public function index()
    {
        $jadwalKirims = JadwalKirim::with('salesOrder')->get();
        return view('admin.JadwalKirim.index', compact('jadwalKirims'));
    }

    public function create()
    {
        $salesOrders = SalesOrder::whereDoesntHave('jadwalKirim')->get();
        return view('admin.JadwalKirim.create', compact('salesOrders'));
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
                'po_number' => $salesOrder->po_number,
                'discount' => $salesOrder->discount,
                'down_payment' => $salesOrder->down_payment,
                'grand_total' => $salesOrder->grand_total,
                'discount_type' => $salesOrder->discount_type,
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
            'delivery_date' => 'required|date|after_or_equal:today',
            'keterangan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
        ]);
        $salesOrder = SalesOrder::find($request->sales_order_id);



        if (JadwalKirim::where('sales_order_id', $salesOrder->id)->exists()) {
            return redirect()->back()->withErrors(['error' => 'Jadwal Kirim untuk nomor SO ini sudah dibuat.']);
        }

        JadwalKirim::create($request->all());
        $this->logActivity('created', JadwalKirim::class, $salesOrder->id, 'Jadwal Kirim Terbuat.');
        return redirect()->route('admin.JadwalKirim.index')->with('success', 'Jadwal Kirim berhasil ditambahkan.');
    }

    public function edit(JadwalKirim $jadwalKirim)
    {
        $salesOrders = SalesOrder::all();
        return view('admin.JadwalKirim.edit', compact('jadwalKirim', 'salesOrders'));
    }

    public function update(Request $request, JadwalKirim $jadwalKirim)
    {
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'delivery_date' => 'required|date|after_or_equal:today',
            'keterangan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
        ]);

        $jadwalKirim->update($request->all());
        $this->logActivity('created', JadwalKirim::class, $jadwalKirim->id, 'Jadwal Kirim Terganti.');
        return redirect()->route('admin.JadwalKirim.index')->with('success', 'Jadwal Kirim berhasil diperbarui.');
    }

    public function show(JadwalKirim $jadwalKirim)
    {
        return view('admin.JadwalKirim.show', compact('jadwalKirim'));
    }

    public function destroy(JadwalKirim $jadwalKirim)
    {
        $jadwalKirim->delete();
        $this->logActivity('created', JadwalKirim::class, $jadwalKirim->id, 'Jadwal Kirim Terhapus.');
        return redirect()->route('admin.JadwalKirim.index')->with('success', 'Jadwal Kirim berhasil dihapus.');
    }

    public function printPDF(JadwalKirim $jadwalKirim)
    {
        $jadwalKirim->load('salesOrder', 'salesOrder.details'); // Load related models if needed
        $pdf = FacadePdf::loadView('admin.JadwalKirim.pdf', compact('jadwalKirim'));
        return $pdf->download('JadwalKirim_' . $jadwalKirim->id . '.pdf');
    }

    protected function logActivity($action, $modelType, $modelId, $description = null)
    {
        \App\Models\ActivityLog::create([
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'user_id' => auth()->id(),
            'description' => $details['description'] ?? null,
            'old_data' => json_encode($details['old_data'] ?? null),
            'new_data' => json_encode($details['new_data'] ?? null),
        ]);
    }

    public function restore($id)
    {
        $jadwalKirim = JadwalKirim::withTrashed()->findOrFail($id);
        $jadwalKirim->restore();

        // Restore details
        foreach ($jadwalKirim->details()->withTrashed()->get() as $detail) {
            $detail->restore();
        }

        $this->logActivity('restored', JadwalKirim::class, $jadwalKirim->id, 'Sales Order restored.');

        return redirect()->route('admin.SalesOrders.index')
            ->with('success', 'Sales Order restored successfully.');
    }
}
