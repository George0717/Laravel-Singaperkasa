<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
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

        return view('pages.SuratJalan.index', compact('suratJalans'));
    }



    public function create()
    {
        $salesOrders = SalesOrder::with('details')->get();
        $nextSuratJalanNumber = $this->generateSuratJalanNumber(); // Panggil method untuk generate nomor
        return view('pages.SuratJalan.create', compact('salesOrders', 'nextSuratJalanNumber'));
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

            // Convert tanggal_pengiriman to Carbon instance
            $tanggalPengiriman = Carbon::parse($validatedData['tanggal_pengiriman']);

            $suratJalan = DB::transaction(function () use ($validatedData, $tanggalPengiriman) {
                $salesOrder = SalesOrder::findOrFail($validatedData['sales_order_id']);

                $suratJalan = SuratJalan::create([
                    'sales_order_id' => $validatedData['sales_order_id'],
                    'plat_angkutan' => $validatedData['plat_angkutan'],
                    'tanggal_pengiriman' => $tanggalPengiriman->format('Y-m-d'),
                    'no_surat_jalan' => $this->generateSuratJalanNumber(),
                ]);

                foreach ($validatedData['items'] as $item) {
                    SuratJalanDetail::create([
                        'surat_jalan_id' => $suratJalan->id,
                        'sales_order_detail_id' => $item['id'],
                        'quantity' => $item['quantity'],
                    ]);

                    $salesOrderDetail = SalesOrderDetail::find($item['id']);
                    $salesOrderDetail->quantity -= $item['quantity'];
                    $salesOrderDetail->save();
                }

                return $suratJalan;
            });

            return redirect()->route('suratJalan.index')->with('success', 'Surat Jalan berhasil disimpan!');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error storing Surat Jalan: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan Surat Jalan.');
        }
    }

    // private function generateSuratJalanNumber()
    // {
    //     $latestSuratJalan = SuratJalan::latest('id')->first();
    //     $nextNumber = $latestSuratJalan ? sprintf('%06d', $latestSuratJalan->id + 1) : '000001';
    //     return $nextNumber;
    // }


    public function show(SuratJalan $suratJalan)
    {
        $suratJalan->load('suratJalanDetails.salesOrderDetail');
        return view('pages.SuratJalan.show', compact('suratJalan'));
    }

    public function edit(SuratJalan $suratJalan)
    {
        // Muat relasi salesOrderDetails ke dalam suratJalan
        $suratJalan->load('suratJalanDetails.salesOrderDetail');
        
        // Ambil daftar sales orders untuk dropdown
        $salesOrders = SalesOrder::all();
    
        // Ambil nomor surat jalan saat ini atau generate yang baru jika belum ada
        $nextSuratJalanNumber = $suratJalan->no_surat_jalan ?? $this->generateSuratJalanNumber();
    
        return view('pages.SuratJalan.edit', compact('suratJalan', 'salesOrders', 'nextSuratJalanNumber'));
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
            }
        });
    
        return redirect()->route('suratJalan.index');
    }
    

    public function destroy(SuratJalan $suratJalan)
    {
        DB::transaction(function () use ($suratJalan) {
            // Restore stock if not sent
            foreach ($suratJalan->suratJalanDetails as $detail) {
                $salesOrderDetail = SalesOrderDetail::find($detail->sales_order_detail_id);
                $salesOrderDetail->quantity += $detail->quantity;
                $salesOrderDetail->save();
            }

            $suratJalan->delete();
        });

        return redirect()->route('suratJalan.index')->with('success', 'Surat Jalan berhasil dihapus dan stok dikembalikan!');
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
}
