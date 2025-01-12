<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\StockHistory;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SuratJalanController extends Controller
{
    public function index(Request $request)
    {
        $customerName = $request->input('customer_name');
        $deliveryDate = $request->input('delivery_date');

        $query = SuratJalan::query();

        if ($customerName) {
            $query->whereHas('salesOrder', function ($q) use ($customerName) {
                $q->where('customer_name', 'like', "%{$customerName}%");
            });
        }

        if ($deliveryDate) {
            $query->whereDate('tanggal_pengiriman', $deliveryDate);
        }

        $suratJalans = $query->paginate(10); // Adjust the number of items per page

        return view('superAdmin.SuratJalan.index', compact('suratJalans'));
    }



    public function create()
    {
        $salesOrders = SalesOrder::with('details')->get();
        $nextSuratJalanNumber = $this->generateSuratJalanNumber(); // Panggil method untuk generate nomor
        return view('superAdmin.SuratJalan.create', compact('salesOrders', 'nextSuratJalanNumber'));
    }


public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'plat_angkutan' => 'required|string|max:255',
            'tanggal_pengiriman' => 'required|date',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:sales_order_details,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Cek jadwal kirim
        $salesOrder = SalesOrder::findOrFail($validatedData['sales_order_id']);
        $jadwalKirim = $salesOrder->jadwalKirim;

        if ($validatedData['tanggal_pengiriman'] > $jadwalKirim->delivery_date) {
            return back()->withErrors([
                'tanggal_pengiriman' => 'Tanggal pengiriman tidak boleh melebihi jadwal kirim (' . $jadwalKirim->delivery_date . ').'
            ]);
        }

        // Convert tanggal_pengiriman to Carbon instance
        $tanggalPengiriman = Carbon::parse($validatedData['tanggal_pengiriman']);

        // Mulai transaction untuk memastikan perubahan stok hanya saat Surat Jalan dibuat
        $suratJalan = DB::transaction(function () use ($validatedData, $tanggalPengiriman, $salesOrder) {
            // Buat Surat Jalan
            $suratJalan = SuratJalan::create([
                'sales_order_id' => $validatedData['sales_order_id'],
                'plat_angkutan' => $validatedData['plat_angkutan'],
                'tanggal_pengiriman' => $tanggalPengiriman->format('Y-m-d'),
                'no_surat_jalan' => $this->generateSuratJalanNumber(),
            ]);

            // Periksa dan kurangi stok hanya jika barang tersedia
            foreach ($validatedData['items'] as $item) {
                $salesOrderDetail = SalesOrderDetail::find($item['id']);

                // Pastikan stok cukup untuk pengiriman
                if ($salesOrderDetail->quantity < $item['quantity']) {
                    throw new \Exception('Stok barang ' . $salesOrderDetail->item_name . ' tidak cukup untuk pengiriman.');
                }

                // Membuat Surat Jalan Detail
                SuratJalanDetail::create([
                    'surat_jalan_id' => $suratJalan->id,
                    'sales_order_detail_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'item_name' => $item['item_name']
                ]);

                // Kurangi stok yang dikirim
                $salesOrderDetail->quantity -= $item['quantity'];
                $salesOrderDetail->save();  // Simpan perubahan stok
            }

            return $suratJalan;
        });

        return redirect()->route('superAdmin.suratJalan.index')->with('success', 'Surat Jalan berhasil disimpan!');
    } catch (\Exception $e) {
        // Log error jika ada
        Log::error('Error storing Surat Jalan: ' . $e->getMessage());

        // Redirect back with error message
        return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan Surat Jalan: ' . $e->getMessage());
    }
}

    public function show(SuratJalan $suratJalan)
    {
        $suratJalan->load('suratJalanDetails.salesOrderDetail');
        return view('superAdmin.SuratJalan.show', compact('suratJalan'));
    }

    public function edit(SuratJalan $suratJalan)
    {
        // Muat relasi salesOrderDetails ke dalam suratJalan
        $suratJalan->load('suratJalanDetails.salesOrderDetail');

        // Ambil daftar sales orders untuk dropdown
        $salesOrders = SalesOrder::all();

        // Ambil nomor surat jalan saat ini atau generate yang baru jika belum ada
        $nextSuratJalanNumber = $suratJalan->no_surat_jalan ?? $this->generateSuratJalanNumber();

        return view('superAdmin.SuratJalan.edit', compact('suratJalan', 'salesOrders', 'nextSuratJalanNumber'));
    }

    public function update(Request $request, SuratJalan $suratJalan)
    {
        DB::transaction(function () use ($request, $suratJalan) {
            // Update Surat Jalan
            $suratJalan->update([
                'plat_angkutan' => $request->plat_angkutan,
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                'no_surat_jalan' => $request->nomor_surat_jalan, // Tambahkan ini
            ]);

            // Remove existing details
            $suratJalan->suratJalanDetails()->delete();

            foreach ($request->items as $item) {
                SuratJalanDetail::create([
                    'surat_jalan_id' => $suratJalan->id,
                    'sales_order_detail_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);

                // Update Sales Order quantity
                $salesOrderDetail = SalesOrderDetail::find($item['id']);
                $salesOrderDetail->quantity -= $item['quantity'];
                $salesOrderDetail->save();

                // Simpan riwayat stok
                StockHistory::create([
                    'sales_order_id' => $salesOrderDetail->sales_order_id,
                    'item_name' => $salesOrderDetail->item_name,
                    'change_quantity' => -$item['quantity'],  // Mengurangi stok
                    'reason' => 'Pengiriman Barang - Surat Jalan No. ' . $suratJalan->no_surat_jalan,
                    'date' => Carbon::now()->format('Y-m-d'),
                ]);
            }
        });

        return redirect()->route('superAdmin.suratJalan.index');
    }


    public function destroy(SuratJalan $suratJalan)
    {
        DB::transaction(function () use ($suratJalan) {
            // Restore stock if not sent
            foreach ($suratJalan->suratJalanDetails as $detail) {
                $salesOrderDetail = SalesOrderDetail::find($detail->sales_order_detail_id);
                $salesOrderDetail->quantity += $detail->quantity; // Mengembalikan stok
                $salesOrderDetail->save();
            }

            $suratJalan->delete();
        });

        return redirect()->route('superAdmin.suratJalan.index')->with('success', 'Surat Jalan berhasil dihapus dan stok dikembalikan!');
    }



    public function generatePDF(SuratJalan $suratJalan)
    {
        $suratJalan->load('suratJalanDetails.salesOrderDetail');
        $pdf = FacadePdf::loadView('pages.suratJalan.pdf', compact('suratJalan'));
        return $pdf->download('surat_jalan_' . $suratJalan->no_surat_jalan . '.pdf');
    }

    private function generateSuratJalanNumber()
    {
        $latest = SuratJalan::latest('id')->first();
        $nextNumber = $latest ? sprintf('%06d', $latest->id + 1) : '000001';
        return $nextNumber;
    }

    public function getSalesOrderDetails(Request $request)
    {
        $salesOrder = SalesOrder::findOrFail($request->sales_order_id);

        // Buat nomor surat jalan jika belum ada
        $suratJalanNumber = $this->generateSuratJalanNumber();

        return response()->json([
            'no_surat_jalan' => $suratJalanNumber,
            'sales_order' => $salesOrder,
            'details' => $salesOrder->details,
        ]);
    }

    public function showStockHistory()
    {
        $histories = StockHistory::with('salesOrder')->get();
        return view('pages.StockBarangSO.show', compact('histories'));
    }
}
