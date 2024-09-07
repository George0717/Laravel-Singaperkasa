<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SalesOrder;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('salesOrder')->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $salesOrders = SalesOrder::all();
        return view('invoices.create', compact('salesOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'invoice_number' => 'required|unique:invoices',
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'down_payment' => 'nullable|numeric',
            'vat' => 'nullable|numeric',
            'grand_total' => 'required|numeric',
            'payment_schedule_type' => 'required|string',
        ]);

        Invoice::create($request->all());
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $salesOrders = SalesOrder::all();
        return view('invoices.edit', compact('invoice', 'salesOrders'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $invoice->id,
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'down_payment' => 'nullable|numeric',
            'vat' => 'nullable|numeric',
            'grand_total' => 'required|numeric',
            'payment_schedule_type' => 'required|string',
        ]);

        $invoice->update($request->all());
        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
