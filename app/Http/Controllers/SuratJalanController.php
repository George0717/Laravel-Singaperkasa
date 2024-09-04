<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

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
    return view('pages.SuratJalan.create', compact('salesOrders'));
}


    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $salesOrder = SalesOrder::findOrFail($request->sales_order_id);

            // Create Surat Jalan
            $suratJalan = SuratJalan::create([
                'sales_order_id' => $request->sales_order_id,
                'plat_angkutan' => $request->plat_angkutan,
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                'no_surat_jalan' => $this->generateSuratJalanNumber(),
            ]);

            foreach ($request->items as $item) {
                $suratJalanDetail = SuratJalanDetail::create([
                    'surat_jalan_id' => $suratJalan->id,
                    'sales_order_detail_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);

                // Reduce quantity in Sales Order
                $salesOrderDetail = SalesOrderDetail::find($item['id']);
                $salesOrderDetail->quantity -= $item['quantity'];
                $salesOrderDetail->save();
            }
        });

        return redirect()->route('suratJalan.index');
    }

    public function show(SuratJalan $suratJalan)
    {
        $suratJalan->load('suratJalanDetails.salesOrderDetail');
        return view('pages.SuratJalan.show', compact('suratJalan'));
    }

    public function edit(SuratJalan $suratJalan)
    {
        $salesOrders = SalesOrder::all();
        return view('pages.SuratJalan.edit', compact('suratJalan', 'salesOrders'));
    }

    public function update(Request $request, SuratJalan $suratJalan)
    {
        DB::transaction(function () use ($request, $suratJalan) {
            $suratJalan->update([
                'plat_angkutan' => $request->plat_angkutan,
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
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
        $suratJalan->delete();
        return redirect()->route('suratJalan.index');
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
}
