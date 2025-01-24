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


class AdminSuratJalanController extends Controller
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

        return view('admin.SuratJalan.index', compact('suratJalans'));
    }



    public function create()
    {
        $salesOrders = SalesOrder::with('details')->get();
        $nextSuratJalanNumber = $this->generateSuratJalanNumber(); // Panggil method untuk generate nomor
        return view('admin.SuratJalan.create', compact('salesOrders', 'nextSuratJalanNumber'));
    }


    public function store(Request $request)
    {
        // dd($request);

        try {
            $validatedData = $request->validate([
                'sales_order_id' => 'required|exists:sales_orders,id',
                'plat_angkutan' => 'required|string|max:255',
                'tanggal_pengiriman' => 'required|date',
                'items' => 'required|array',
                'items.*.sales_order_details_id' => 'required|exists:sales_order_details,id', // Ubah dari `sales_order_details_id` ke `id`
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            // Cek jadwal kirim
            $salesOrder = SalesOrder::findOrFail($validatedData['sales_order_id']);
            $jadwalKirim = $salesOrder->jadwalKirim;
            // dd($salesOrder->id);

            // Cek apakah tanggal pengiriman melebihi jadwal kirim
            if ($validatedData['tanggal_pengiriman'] > $jadwalKirim->delivery_date) {
                return back()->withErrors([
                    'tanggal_pengiriman' => 'Tanggal pengiriman tidak boleh melebihi jadwal kirim (' . $jadwalKirim->delivery_date . ').'
                ]);
            }

            $normalizedItems = array_values($request->input('items', []));
            $request->merge(['items' => $normalizedItems]);
            // Convert tanggal_pengiriman to Carbon instance
            $tanggalPengiriman = Carbon::parse($validatedData['tanggal_pengiriman']);

            // Mulai transaction untuk memastikan perubahan stok hanya saat Surat Jalan dibuat
            $suratJalan = DB::transaction(function () use ($validatedData, $tanggalPengiriman, $salesOrder) {
                // Buat Surat Jalan secara manual
                $suratJalan = new SuratJalan();
                $suratJalan->sales_order_id = $salesOrder->id;
                $suratJalan->plat_angkutan = $validatedData['plat_angkutan'];
                $suratJalan->tanggal_pengiriman = $tanggalPengiriman->format('Y-m-d');
                $suratJalan->no_surat_jalan = $this->generateSuratJalanNumber();
                $suratJalan->save(); // Simpan Surat Jalan

                // Periksa dan kurangi stok hanya jika barang tersedia
                foreach ($validatedData['items'] as $item) {
                    $salesOrderDetail = SalesOrderDetail::find($item['sales_order_details_id']);

                    // Pastikan stok cukup untuk pengiriman
                    if ($salesOrderDetail->quantity < $item['quantity']) {
                        throw new \Exception('Stok barang ' . $salesOrderDetail->item_name . ' tidak cukup untuk pengiriman.');
                    }

                    // Membuat Surat Jalan Detail
                    $suratJalanDetail = new SuratJalanDetail();
                    $suratJalanDetail->surat_jalan_id = $suratJalan->id;
                    $suratJalanDetail->sales_order_details_id = $item['sales_order_details_id'];
                    $suratJalanDetail->quantity = $item['quantity'];
                    $suratJalanDetail->save(); // Simpan Surat Jalan Detail

                    // Kurangi stok yang dikirim
                    $salesOrderDetail->quantity -= $item['quantity'];
                    $salesOrderDetail->save();  // Simpan perubahan stok
                }

                return $suratJalan;
            });
            $this->logActivity('created', SuratJalan::class, $suratJalan->id, 'Surat Jalan Terbuat.');
            return redirect()->route('admin.suratJalan.index')->with('success', 'Surat Jalan berhasil disimpan!');
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
        return view('admin.SuratJalan.show', compact('suratJalan'));
    }

    public function edit(SuratJalan $suratJalan)
    {
        // Muat relasi salesOrderDetails ke dalam suratJalan
        $suratJalan->load('suratJalanDetails.salesOrderDetail');

        // Ambil daftar sales orders untuk dropdown
        $salesOrders = SalesOrder::all();

        // Ambil nomor surat jalan saat ini atau generate yang baru jika belum ada
        $nextSuratJalanNumber = $suratJalan->no_surat_jalan ?? $this->generateSuratJalanNumber();

        return view('admin.SuratJalan.edit', compact('suratJalan', 'salesOrders', 'nextSuratJalanNumber'));
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


            $this->logActivity('created', SuratJalan::class, $suratJalan->id, 'Surat Jalan Terbuat.');
        return redirect()->route('admin.suratJalan.index');
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
        $this->logActivity('created', SuratJalan::class, $suratJalan->id, 'Surat Jalan Terhapus.');
        return redirect()->route('admin.suratJalan.index')->with('success', 'Surat Jalan berhasil dihapus dan stok dikembalikan!');
    }



    public function generatePDF(SuratJalan $suratJalan)
    {
        $suratJalan->load('suratJalanDetails.salesOrderDetail');
        $pdf = FacadePdf::loadView('admin.suratJalan.pdf', compact('suratJalan'));
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
        $suratJalan = SuratJalan::withTrashed()->findOrFail($id);
        $suratJalan->restore();

        // Restore details
        foreach ($suratJalan->details()->withTrashed()->get() as $detail) {
            $detail->restore();
        }

        $this->logActivity('restored', SuratJalan::class, $suratJalan->id, 'Sales Order restored.');

        return redirect()->route('admin.SalesOrders.index')
            ->with('success', 'Sales Order restored successfully.');
    }
}
