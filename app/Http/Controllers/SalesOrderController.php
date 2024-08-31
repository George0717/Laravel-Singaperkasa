<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with('details')->get();
        return view('pages.SalesOrder.index', compact('salesOrders'));
    }

    public function create()
    {
        $latestOrder = SalesOrder::latest()->first();
        $nextSoNumber = $latestOrder ? str_pad((int)substr($latestOrder->so_number, 3) + 1, 5, '0', STR_PAD_LEFT) : '00001';

        return view('pages.SalesOrder.create', ['soNumber' => $nextSoNumber]);
    }

    public function store(Request $request)
    {
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
            'due_date' => 'nullable|date',
            'item_name.*' => 'required|string',
            'item_qty.*' => 'required|numeric',
            'item_price.*' => 'required|numeric',
            'po_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $salesOrder = new SalesOrder();
        $salesOrder->customer_name = $validated['customer_name'];
        $salesOrder->customer_address = $validated['customer_address'];
        $salesOrder->po_date = $validated['po_date'];
        $salesOrder->po_number = $validated['po_number'];
        $salesOrder->so_number = 'SO' . time();
        $salesOrder->discount = $validated['discount'];
        $salesOrder->discount_type = $validated['discount_type'];
        $salesOrder->vat = $validated['vat'];
        $salesOrder->down_payment = $validated['down_payment'];
        $salesOrder->payment_type = $validated['payment_type'];
        $salesOrder->due_date = $validated['due_date'];

        if ($request->hasFile('po_photo')) {
            $path = $request->file('po_photo')->store('po_photos', 'public');
            $salesOrder->po_photo = $path;
        }

        $subTotal = 0;
        foreach ($request->item_qty as $index => $qty) {
            $subTotal += $qty * $request->item_price[$index];
        }

        $discountAmount = $validated['discount_type'] == 'percent'
            ? ($subTotal * $validated['discount']) / 100
            : $validated['discount'];

        $vatAmount = ($subTotal * $validated['vat']) / 100;
        $grandTotal = ($subTotal + $vatAmount) - $discountAmount - $validated['down_payment'];

        // Save the calculated grand total to the salesOrder object
        $salesOrder->grand_total = $grandTotal;

        $salesOrder->save();

        foreach ($validated['item_name'] as $index => $itemName) {
            $itemDetail = new SalesOrderDetail();
            $itemDetail->sales_order_id = $salesOrder->id;
            $itemDetail->item_name = $itemName;
            $itemDetail->quantity = $validated['item_qty'][$index];
            $itemDetail->price = $validated['item_price'][$index];
            $itemDetail->save();
        }

        return redirect()->route('SalesOrders.index')->with('success', 'Sales Order created successfully.');
    }



    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('details');
    
        // Ensure the details are not null
        if ($salesOrder->details === null) {
            $salesOrder->details = collect(); // Return an empty collection instead of null
        }
    
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
    $salesOrder->load('details');
    $pdf = FacadePdf::loadView('pages.SalesOrder.pdf', compact('salesOrder'));
    return $pdf->download('SalesOrder_'.$salesOrder->so_number.'.pdf');
}
}
