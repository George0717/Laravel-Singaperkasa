<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    // Method untuk menampilkan semua invoice
    public function index()
    {
        $invoices = Invoice::all();
        return view('pages.Invoice.index', compact('invoices'));
    }

    // Method untuk form create invoice
    // InvoiceController.php

    public function create()
    {
        // Ambil semua sales orders
        $salesOrders = SalesOrder::all();

        // Dapatkan nomor invoice terbaru
        $latestInvoice = Invoice::orderBy('created_at', 'desc')->first();
        $invoiceNumber = 'INVOICE00001';

        if ($latestInvoice) {
            $lastNumber = (int) substr($latestInvoice->invoice_number, 7);
            $invoiceNumber = 'INVOICE' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        return view('pages.Invoice.create', compact('salesOrders', 'invoiceNumber'));
    }

    // Method untuk menyimpan invoice baru
    // InvoiceController.php

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'invoice_number' => 'required|unique:invoices',
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'down_payment' => 'nullable|numeric',
            'vat' => 'nullable|numeric',
            'grand_total' => 'required|numeric',
            'payment_schedule_type' => 'required|string',
        ], [
            'sales_order_id.required' => 'Sales Order harus dipilih.',
            'invoice_number.required' => 'Nomor Invoice wajib diisi.',
            'invoice_number.unique' => 'Nomor Invoice sudah ada, silakan gunakan nomor lain.',
            'subtotal.required' => 'Subtotal harus diisi.',
            'grand_total.required' => 'Grand Total harus diisi.',
        ]);

        // Simpan data invoice
        Invoice::create($request->all());

        // Redirect ke halaman index invoice
        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dibuat.');
    }


    // Method untuk menampilkan detail invoice
    public function show(Invoice $invoice)
    {
        return view('pages.Invoice.show', compact('invoice'));
    }

    // Method untuk form edit invoice
    public function edit(Invoice $invoice)
    {
        $salesOrders = SalesOrder::all();
        return view('pages.Invoice.edit', compact('invoice', 'salesOrders'));
    }

    // Method untuk update invoice
    public function update(Request $request, Invoice $invoice)
    {
        // Validasi input saat update
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $invoice->id,
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'down_payment' => 'nullable|numeric',
            'vat' => 'nullable|numeric',
            'grand_total' => 'required|numeric',
            'payment_schedule_type' => 'required|string',
        ], [
            'sales_order_id.required' => 'Sales Order harus dipilih.',
            'invoice_number.required' => 'Nomor Invoice wajib diisi.',
            'invoice_number.unique' => 'Nomor Invoice sudah ada, silakan gunakan nomor lain.',
            'subtotal.required' => 'Subtotal harus diisi.',
            'grand_total.required' => 'Grand Total harus diisi.',
        ]);

        // Update data invoice
        $invoice->update($request->all());

        // Redirect ke halaman index invoice
        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    // Method untuk menghapus invoice
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dihapus.');
    }

    public function getSalesOrderData($id)
    {
        $salesOrder = SalesOrder::with('details')->find($id);

        if (!$salesOrder) {
            return response()->json(['message' => 'Sales Order tidak ditemukan'], 404);
        }

        $itemsStatus = $salesOrder->details->map(function ($detail) {
            $status = $detail->quantity > $detail->quantity_shipped ? 'Belum sepenuhnya dikirim' : 'Semua barang telah dikirim';
            return [
                'item_name' => $detail->item_name,
                'quantity' => $detail->quantity,
                'quantity_shipped' => $detail->quantity_shipped,
                'status' => $status,
            ];
        })->toArray();  // Ensure it's an array

        return response()->json([
            'customer_name' => $salesOrder->customer_name,
            'customer_address' => $salesOrder->customer_address,
            'po_date' => $salesOrder->po_date,
            'po_number' => $salesOrder->po_number,
            'factory_name' => $salesOrder->factory_name,
            'factory_address' => $salesOrder->factory_address,
            'address' => $salesOrder->address,
            'phone' => $salesOrder->phone,
            'items' => $itemsStatus,
            'shipments' => [],  // Ensure this is an array
            'due_date' => $salesOrder->due_date,
        ]);
    }
}
