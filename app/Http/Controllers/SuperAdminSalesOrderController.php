<?php

namespace App\Http\Controllers;

use App\Mail\DueDateReminder;
use App\Mail\SendEmail;
use App\Models\JadwalKirim;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\StockBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class SuperAdminSalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::all();

        return view('superAdmin.SalesOrder.index', compact('salesOrders'));
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

        return view('superAdmin.dashboard.dashboard', compact(
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

    public function create()
    {
        $stockBarangs = StockBarang::all(['id', 'nama_barang', 'jumlah_barang', 'tipe_barang']);

        return view('superAdmin.SalesOrder.create', compact('stockBarangs'));
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
            return redirect()->route('superAdmin.SalesOrders.index')->with('success', 'Sales Order created successfully.');
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
        return view('superAdmin.SalesOrder.show', compact('salesOrder'));
    }



    public function edit($id)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $stockBarangs = StockBarang::all();

        // Mendefinisikan item options, ini bisa diambil dari database atau sumber lain
        $itemOptions = [
            "Paku" => 500000,
            "Baja" => 2000000,
            "Besi Panjang" => 2500000,
        ];

        return view('superAdmin.SalesOrder.edit', [
            'salesOrder' => $salesOrder,
            'itemOptions' => $itemOptions, // Pastikan itemOptions diteruskan ke view
        ], compact('stockBarangs'));
    }


    public function update(Request $request, $id)
    {
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

        $salesOrder = SalesOrder::findOrFail($id);
        $oldData = $salesOrder->toArray();

        // Update Sales Order fields
        $salesOrder->customer_name = $validated['customer_name'];
        $salesOrder->discount = $validated['discount'];
        $salesOrder->discount_type = $validated['discount_type'];
        $salesOrder->vat = $validated['vat'];
        $salesOrder->down_payment = $validated['down_payment'];
        $salesOrder->payment_type = $validated['payment_type'];
        $salesOrder->due_date = $validated['due_date'];

        if ($request->hasFile('po_photo')) {
            $poPhoto = $request->file('po_photo')->store('po_photos', 'public');
            $salesOrder->po_photo = $poPhoto;
        }

        $salesOrder->save();

        // Handle items (details)
        $newItems = collect($validated['items']);
        $existingItems = $salesOrder->details->keyBy('stock_barang_id');
        $salesOrder->details()->delete();


        // Update or create items
        foreach ($newItems as $item) {
            $stockBarang = StockBarang::find($item['stock_barang_id']);

            if (!$stockBarang || $stockBarang->jumlah_barang < $item['jumlah_barang']) {
                throw new \Exception("Stock for {$stockBarang->nama_barang} is insufficient.");
            }

            if ($existingItems->has($item['stock_barang_id'])) {
                // Update existing item
                $detail = $existingItems[$item['stock_barang_id']];
                $detail->update([
                    'quantity' => $item['jumlah_barang'],
                    'price' => $item['price'],
                    'per' => $item['per'],
                    'total' => $item['jumlah_barang'] * $item['price'],
                ]);
            } else {
                // Create new item
                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'stock_barang_id' => $item['stock_barang_id'],
                    'item_name' => $stockBarang->nama_barang,
                    'quantity' => $item['jumlah_barang'],
                    'price' => $item['price'],
                    'per' => $item['per'],
                    'total' => $item['jumlah_barang'] * $item['price'],
                ]);

                // Deduct stock for new items
                $stockBarang->decrement('jumlah_barang', $item['jumlah_barang']);
            }
        }

        // Delete removed items
        $removedItems = $existingItems->keys()->diff($newItems->pluck('stock_barang_id'));
        foreach ($removedItems as $removedItemId) {
            $detail = $existingItems[$removedItemId];
            StockBarang::find($removedItemId)->increment('jumlah_barang', $detail->quantity);
            $detail->delete();
        }

        // Log changes
        $this->logActivity('updated', SalesOrder::class, $salesOrder->id, 'Sales Order updated.', $oldData, $salesOrder->toArray());

        return redirect()->route('superAdmin.SalesOrders.index')->with('success', 'Sales Order updated successfully.');
    }



    public function destroy(SalesOrder $salesOrder)
    {
        // Temukan pesanan berdasarkan ID
        DB::transaction(function () use ($salesOrder) {
            foreach ($salesOrder->details as $detail) {
                $stockBarang = StockBarang::find($detail->stock_barang_id);
                $stockBarang->jumlah_barang += $detail->jumlah_barang; // Tambahkan jumlah barang ke stok
                $stockBarang->save();
            }

            // Hapus pesanan
            $this->logActivity('deleted', SalesOrder::class, $salesOrder->id, 'Sales Order deleted.');
            $salesOrder->delete();
        });

        // Kembalikan stok barang terkait

        return redirect()->route('superAdmin.SalesOrders.index')
            ->with('success', 'Pesanan berhasil dihapus, dan stok barang dikembalikan.');
    }


    public function printPDF(SalesOrder $salesOrder)
    {
        // Memuat detail dari SalesOrder
        $salesOrder->load('details');

        // Menyusun nama file PDF tanpa karakter yang tidak diizinkan
        $fileName = 'SalesOrder_' . str_replace(['/', '\\'], '-', $salesOrder->so_number) . '.pdf';

        // Membuat file PDF dan mendownloadnya
        $pdf = FacadePdf::loadView('superAdmin.SalesOrder.pdf', compact('salesOrder'));
        return $pdf->download($fileName);
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

        return redirect()->route('superAdmin.SalesOrders.index')
            ->with('success', 'Sales Order restored successfully.');
    }
}
