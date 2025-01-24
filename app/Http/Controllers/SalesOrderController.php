<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\JadwalKirim;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\StockBarang;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with('details')->get();

        return view('pages.SalesOrder.index', compact('salesOrders'));
    }

    public function create()
    {
        $stockBarangs = StockBarang::all(['id', 'nama_barang', 'jumlah_barang', 'tipe_barang']);

        return view('pages.SalesOrder.create', compact('stockBarangs'));
    }


    public function dashboard(Request $request)
{
    // Default values untuk filter
    $year = $request->input('year', date('Y')); // Tahun sekarang jika tidak dipilih
    $month = $request->input('month'); // Null jika tidak dipilih
    $start_date = $request->input('start_date'); // Tanggal mulai filter
    $end_date = $request->input('end_date'); // Tanggal akhir filter

    // Query dasar
    $query = SalesOrder::query();

    // Filter berdasarkan rentang waktu jika ada
    if ($start_date && $end_date) {
        $query->whereBetween('created_at', [$start_date, $end_date]);
    } elseif ($month) {
        // Filter berdasarkan bulan dan tahun jika rentang waktu tidak dipilih
        $query->whereYear('created_at', $year)
              ->whereMonth('created_at', $month);
    } else {
        // Filter berdasarkan tahun jika tidak ada rentang waktu atau bulan
        $query->whereYear('created_at', $year);
    }

    // Ambil data sesuai filter
    $salesOrders = $query->get();

    // Data untuk grafik
    if ($start_date && $end_date) {
        // Jika menggunakan rentang waktu, grup data berdasarkan hari
        $groupedOrders = $salesOrders->groupBy(function ($order) {
            return \Carbon\Carbon::parse($order->created_at)->format('d F Y');
        });
    } elseif ($month) {
        // Jika menggunakan filter bulan, grup data berdasarkan tanggal
        $groupedOrders = $salesOrders->groupBy(function ($order) {
            return \Carbon\Carbon::parse($order->created_at)->format('d F Y');
        });
    } else {
        // Jika menggunakan filter tahun, grup data berdasarkan bulan
        $groupedOrders = $salesOrders->groupBy(function ($order) {
            return \Carbon\Carbon::parse($order->created_at)->format('F');
        });
    }

    $labels = [];
    $data = [];

    foreach ($groupedOrders as $key => $orders) {
        $labels[] = $key;
        $data[] = count($orders);
    }

    // Data untuk kartu
    $totalSalesOrders = SalesOrder::count(); // Total semua Sales Orders
    $currentMonthSalesOrders = SalesOrder::whereYear('created_at', $year)
        ->whereMonth('created_at', date('m'))
        ->count();
    $currentYearSalesOrders = SalesOrder::whereYear('created_at', $year)->count();

    return view('pages.dashboard.dashboard', compact(
        'totalSalesOrders',
        'currentMonthSalesOrders',
        'currentYearSalesOrders',
        'labels',
        'data',
        'month',
        'year',
        'start_date',
        'end_date'
    ));
}

    



public function store(Request $request)
{
    // dd($request);

    $validated = $request->validate([
        'customer_name' => 'required|string|max:255',
        'po_photo' => 'nullable|image',
        'so_number' => 'nullable|string|max:255',
        'discount' => 'nullable|numeric',
        'discount_type' => 'required|string',
        'payment_type' => 'required|string',
        'down_payment' => 'nullable|numeric',
        'vat' => 'nullable|numeric',
        'grand_total' => 'required|numeric',
        'payment_schedule_type' => 'nullable|string',
        'due_date' => 'required|date',
        'items' => 'required|array',
        'items.*.stock_barang_id' => 'required|exists:stock_barang,id',
        'items.*.jumlah_barang' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.total' => 'required|numeric|min:0',
        'items.*.per' => 'required|string',
    ]);


    $romanMonths = [
        1 => 'I',
        2 => 'II',
        3 => 'III',
        4 => 'IV',
        5 => 'V',
        6 => 'VI',
        7 => 'VII',
        8 => 'VIII',
        9 => 'IX',
        10 => 'X',
        11 => 'XI',
        12 => 'XII',
    ];
    $currentMonth = Carbon::now()->format('m');
    $currentYear = Carbon::now()->format('Y');
    $romanMonth = $romanMonths[(int)$currentMonth]; // Konversi angka bulan ke Romawi
    $companyName = "SPA";
    $lastOrder = SalesOrder::whereYear('created_at', $currentYear)
        ->whereMonth('created_at', $currentMonth)
        ->orderBy('id', 'desc')
        ->first();

    $newNumber = $lastOrder ? ((int)Str::before($lastOrder->so_number, '/') + 1) : 1;
    $soNumber = sprintf('%02d/%s/%s/%s', $newNumber, strtoupper($companyName), $romanMonth, $currentYear);

    // Pastikan tidak ada duplikasi
    while (SalesOrder::where('so_number', $soNumber)->exists()) {
        $newNumber++;
        $soNumber = sprintf('%02d/%s/%s/%s', $newNumber, strtoupper($companyName), $romanMonth, $currentYear);
    }

    $items = $request->input('items');
    // $firstitems = head($items);
    // $stockBarangId = $firstitems['stock_barang_id'];
    // dd($stockBarangId);

    // Process each sales order detail
    foreach ($items as $item) {
        $stockBarang = StockBarang::find($item['stock_barang_id']);

        $stockBarangId = $item['stock_barang_id'];

        // Check stock availability
        if ($stockBarang->jumlah_barang < $item['jumlah_barang']) {
            throw new \Exception("Stock for {$stockBarang->nama_barang} is insufficient.");
        }
        // Deduct stock
        $stockBarang->decrement('jumlah_barang', $item['jumlah_barang']);


        $salesOrder = new SalesOrder();
        $salesOrder->customer_name = $validated['customer_name'];
        $salesOrder->so_number = $soNumber;
        $salesOrder->discount = $validated['discount'];
        $salesOrder->discount_type = $validated['discount_type'];
        $salesOrder->vat = $validated['vat'];
        $salesOrder->down_payment = $validated['down_payment'];
        $salesOrder->grand_total = $validated['grand_total'];
        $salesOrder->payment_type = $validated['payment_type'];
        $salesOrder->due_date = $validated['due_date'];
        $salesOrder->stock_barang_id = $stockBarangId;
        JadwalKirim::where('sales_order_id', $salesOrder->id)->delete();
        $salesOrder->save();

        // Create sales order detail
        SalesOrderDetail::create([
            'sales_order_id' => $salesOrder->id,
            'stock_barang_id' => $stockBarangId,
            'item_name' => $stockBarang->nama_barang,
            'quantity' => $item['jumlah_barang'],
            'price' => $item['price'],
            'per' => $item['per'],
            'total' => $item['jumlah_barang'] * $item['price'],
        ]);


        // if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($salesOrder->due_date))) {
        //     Mail::to('radengeorge@mhs.mdp.ac.id')->send(new SendEmail($salesOrder));
        // }
        $this->logActivity('created', SalesOrder::class, $salesOrder->id, 'Sales Order created.');
        return redirect()->route('SalesOrders.index')->with('success', 'Sales Order created successfully.');
    }
}



    public function show(SalesOrder $salesOrder)
    {
        // Memuat relasi 'details' untuk mendapatkan item terkait
        $salesOrder->load('details');

        // Pastikan jika 'details' kosong, ubah menjadi koleksi kosong
        if ($salesOrder->details->isEmpty()) {
            $salesOrder->details = collect();  // Membuat koleksi kosong jika tidak ada detail
        }

        // Kirim data ke tampilan
        return view('pages.SalesOrder.show', compact('salesOrder'));
    }



    public function edit($id)
    {
        $salesOrder = SalesOrder::findOrFail($id);

        // Mendefinisikan item options, ini bisa diambil dari database atau sumber lain
        $itemOptions = [
            "Paku" => 500000,
            "Baja" => 2000000,
            "Besi Panjang" => 2500000,
        ];

        return view('pages.SalesOrder.edit', [
            'salesOrder' => $salesOrder,
            'itemOptions' => $itemOptions, // Pastikan itemOptions diteruskan ke view
        ]);
    }


    public function update(Request $request, SalesOrder $salesOrder)
    {
        Log::info($request->all()); // Log request data for debugging

        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_address' => 'required|string',
                'po_date' => 'required|date',
                'po_number' => 'required|string|max:50',
                'discount' => 'nullable|numeric',
                'discount_type' => 'nullable|string|in:percent,currency',
                'vat' => 'nullable|numeric',
                'down_payment' => 'nullable|numeric',
                'payment_type' => 'required|string',
                'item_name.*' => 'required|string',
                'item_qty.*' => 'required|numeric',
                'item_price.*' => 'required|numeric',
                'po_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ]);

            // Update the existing SalesOrder instance
            $salesOrder->update([
                'customer_name' => $validated['customer_name'],
                'customer_address' => $validated['customer_address'],
                'po_date' => $validated['po_date'],
                'po_number' => $validated['po_number'],
                'discount' => $validated['discount'],
                'discount_type' => $validated['discount_type'],
                'vat' => $validated['vat'],
                'down_payment' => $validated['down_payment'],
                'payment_type' => $validated['payment_type'],
            ]);

            // Handle file upload for PO photo if provided
            if ($request->hasFile('po_photo')) {
                $path = $request->file('po_photo')->store('po_photos', 'public');
                $salesOrder->po_photo = $path;
            }

            // Calculate subtotal, discount, VAT, and grand total
            $subTotal = 0;
            foreach ($request->item_qty as $index => $qty) {
                $subTotal += $qty * $request->item_price[$index];
            }

            $discountAmount = $validated['discount_type'] == 'percent'
                ? ($subTotal * $validated['discount']) / 100
                : $validated['discount'];

            $vatAmount = ($subTotal * $validated['vat']) / 100;
            $grandTotal = ($subTotal + $vatAmount) - $discountAmount - $validated['down_payment'];

            // Update the calculated grand total in the SalesOrder object
            $salesOrder->grand_total = $grandTotal;
            $salesOrder->save();

            // Update associated SalesOrderDetail records
            $salesOrder->details()->delete(); // Optionally delete all existing details first

            foreach ($validated['item_name'] as $index => $itemName) {
                $itemDetail = new SalesOrderDetail();
                $itemDetail->sales_order_id = $salesOrder->id;
                $itemDetail->item_name = $itemName;
                $itemDetail->quantity = $validated['item_qty'][$index];
                $itemDetail->price = $validated['item_price'][$index];
                $itemDetail->save();
            }

            return redirect()->route('SalesOrders.index')->with('success', 'Sales Order updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'There was an error updating the Sales Order. Please try again.');
        }
    }

    public function destroy(SalesOrder $salesOrder)
    {
        $salesOrder->delete();
        return redirect()->route('SalesOrders.index')->with('success', 'Sales Order deleted successfully.');
    }

    public function printPDF(SalesOrder $salesOrder)
    {
        // Memuat detail dari SalesOrder
        $salesOrder->load('details');

        // Menyusun nama file PDF tanpa karakter yang tidak diizinkan
        $fileName = 'SalesOrder_' . str_replace(['/', '\\'], '-', $salesOrder->so_number) . '.pdf';

        // Membuat file PDF dan mendownloadnya
        $pdf = FacadePdf::loadView('pages.SalesOrder.pdf', compact('salesOrder'));
        return $pdf->download($fileName);
    }

    public function getStockBarang(Request $request)
    {
        $stockBarang = StockBarang::findOrFail($request->sales_order_id);

        // Buat nomor surat jalan jika belum ada
        $suratJalanNumber = $this->generateSuratJalanNumber();

        return response()->json([
            'no_surat_jalan' => $suratJalanNumber,
            'sales_order' => $stockBarang,
            'details' => $stockBarang->details,
        ]);
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
        $salesOrder = SalesOrder::withTrashed()->findOrFail($id);
        $salesOrder->restore();

        // Restore details
        foreach ($salesOrder->details()->withTrashed()->get() as $detail) {
            $detail->restore();
        }

        $this->logActivity('restored', SalesOrder::class, $salesOrder->id, 'Sales Order restored.');

        return redirect()->route('SalesOrders.index')
            ->with('success', 'Sales Order restored successfully.');
    }

}
