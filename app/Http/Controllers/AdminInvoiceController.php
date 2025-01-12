<?php

namespace App\Http\Controllers;

use App\Exports\InvoiceExport;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class AdminInvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();
        return view('admin.Invoice.index', compact('invoices'));
    }


    public function create()
    {
        $salesOrders = SalesOrder::all();
        $latestInvoice = Invoice::orderBy('created_at', 'desc')->first();
        $invoiceNumber = 'INVOICE00001';

        if ($latestInvoice) {
            $lastNumber = (int) substr($latestInvoice->invoice_number, 7);
            $invoiceNumber = 'INVOICE' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        return view('admin.Invoice.create', compact('salesOrders', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'invoice_number' => 'required|unique:invoices',
            'subtotal' => 'required|numeric',
        ], [
            'sales_order_id.required' => 'Sales Order harus dipilih.',
            'invoice_number.required' => 'Nomor Invoice wajib diisi.',
            'invoice_number.unique' => 'Nomor Invoice sudah ada, silakan gunakan nomor lain.',
            'subtotal.required' => 'Subtotal harus diisi.',
        ]);

        // Ambil data dari Sales Order
        $salesOrder = SalesOrder::findOrFail($request->sales_order_id);

        // Buat data untuk Invoice
        $invoiceData = [
            'sales_order_id' => $salesOrder->id,
            'invoice_number' => $request->invoice_number,
            'subtotal' => $request->subtotal,
            'discount' => $salesOrder->discount,
            'down_payment' => $salesOrder->down_payment,
            'vat' => $salesOrder->vat,
            'grand_total' => $salesOrder->grand_total,
            'payment_type' => $salesOrder->payment_type,
        ];

        // Simpan data Invoice
        Invoice::create($invoiceData);

        // Redirect ke halaman index invoice
        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dibuat.');
    }



    // Method untuk menampilkan detail invoice
    public function show(Invoice $invoice)
    {
        $invoice->load('salesOrder.suratJalans.suratJalanDetails');
        $salesOrder = $invoice->salesOrder()->with('jadwalKirim')->first();
        return view('admin.Invoice.show', compact('invoice', 'salesOrder'));
    }

    // Method untuk form edit invoice
    public function edit(Invoice $invoice)
    {
        $salesOrders = SalesOrder::all();
        return view('admin.Invoice.edit', compact('invoice', 'salesOrders'));
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
            'payment_type' => 'required|string',
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
        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    // Method untuk menghapus invoice
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dihapus.');
    }

    public function getSalesOrderData($id)
    {
        $salesOrder = SalesOrder::with(['suratJalans.shipments', 'details'])->find($id);

        if (!$salesOrder) {
            return response()->json(['message' => 'Sales Order tidak ditemukan'], 404);
        }

        $subtotal = $salesOrder->details->sum('price'); // Hitung subtotal
        $discount = $salesOrder->discount ?? 0; // Ambil discount
        $downPayment = $salesOrder->down_payment ?? 0; // Ambil down payment
        $vat = $salesOrder->vat ?? 0; // Ambil VAT
        $grandTotal = $subtotal - $discount + $vat; // Hitung grand total

        $itemsStatus = $salesOrder->details->map(function ($detail) {
            $status = $detail->quantity > $detail->quantity_shipped ? 'Belum sepenuhnya dikirim' : 'Semua barang telah dikirim';
            return [
                'item_name' => $detail->item_name,
                'quantity' => $detail->quantity,
                'quantity_shipped' => $detail->quantity_shipped,
                'status' => $status,
            ];
        })->toArray();

        $shipmentsData = $salesOrder->suratJalans->map(function ($suratJalan) {
            return [
                'no_surat_jalan' => $suratJalan->no_surat_jalan ?? 'N/A',
                'tanggal_pengiriman' => $suratJalan->tanggal_pengiriman ?? 'N/A',
                'plat_angkutan' => $suratJalan->plat_angkutan ?? 'N/A',
                'jumlah' => $suratJalan->suratJalanDetails->sum('quantity') ?? 'N/A',
            ];
        });

        return response()->json([
            'customer_name' => $salesOrder->customer_name,
            'customer_address' => $salesOrder->customer_address,
            'payment_type' => $salesOrder->payment_type,
            'po_date' => $salesOrder->po_date,
            'po_number' => $salesOrder->po_number,
            'address' => $salesOrder->address,
            'phone' => $salesOrder->phone,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'down_payment' => $downPayment,
            'vat' => $vat,
            'grand_total' => $grandTotal,
            'items' => $itemsStatus,
            'due_date' => $salesOrder->due_date,
            'shipments' => $shipmentsData, // Mengambil shipment dari suratJalan
            'items' => $itemsStatus,
        ]);
    }

    public function generatePDF(Invoice $invoice)
    {
        $invoice->load('salesOrder.suratJalans.suratJalanDetails');
        $salesOrder = $invoice->salesOrder()->with('jadwalKirim')->first();

        // Generate PDF
        $pdf = FacadePdf::loadView('admin.Invoice.pdf', compact('invoice', 'salesOrder'));

        // Return PDF as a download
        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function generateXLS(Invoice $invoice)
    {
        return Excel::download(new InvoiceExport($invoice->id), 'Invoice-' . $invoice->invoice_number . '.xlsx');
    }
}
