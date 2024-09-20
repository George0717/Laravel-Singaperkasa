@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6 text-center text-primary">Create Invoice</h1>

    <!-- Pesan Sukses -->
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Form untuk membuat invoice -->
    <form action="{{ route('invoice.store') }}" method="POST" class="bg-white p-8 shadow-md rounded-lg">
        @csrf

        <!-- Nomor Invoice -->
        <div class="mb-4">
            <label for="invoice_number" class="block text-sm font-medium text-gray-700">Invoice Number</label>
            <input type="text" id="invoice_number" name="invoice_number" value="{{ $invoiceNumber }}" readonly
                class="form-input mt-1 block w-full bg-gray-100">
            @error('invoice_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pemilihan Sales Order -->
        <div class="mb-4">
            <label for="sales_order_id" class="block text-sm font-medium text-gray-700">Sales Order</label>
            <select id="sales_order_id" name="sales_order_id" class="form-select mt-1 block w-full" required>
                <option value="" disabled selected>Select Sales Order</option>
                @foreach ($salesOrders as $order)
                <option value="{{ $order->id }}">{{ $order->so_number }} - {{ $order->customer_name }}</option>
                @endforeach
            </select>
            @error('sales_order_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Detail Sales Order akan dimuat di sini secara dinamis -->
        <div id="sales-order-details" class="overflow-x-auto mt-6">
            <!-- Konten akan dimasukkan di sini secara dinamis -->
        </div>

        <!-- Detail Invoice -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
            <div class="form-group mb-4">
                <label for="subtotal" class="block text-sm font-medium text-gray-700">Subtotal</label>
                <input type="text" name="subtotal" id="subtotal" class="form-input mt-1 block w-full" readonly
                    value="Rp {{ number_format(old('subtotal', 0), 2, ',', '.') }}"
                    onfocus="this.select(); this.setSelectionRange(0, this.value.length);">
            </div>

            <div class="form-group mb-4">
                <label for="discount" class="block text-sm font-medium text-gray-700">Diskon</label>
                <input type="text" name="discount" id="discount" class="form-input mt-1 block w-full" readonly
                    value="Rp {{ number_format(old('discount', 0), 2, ',', '.') }}"
                    onfocus="this.select(); this.setSelectionRange(0, this.value.length);">
            </div>

            <div class="form-group mb-4">
                <label for="down_payment" class="block text-sm font-medium text-gray-700">Uang Muka</label>
                <input type="text" name="down_payment" id="down_payment" class="form-input mt-1 block w-full" readonly
                    value="Rp {{ number_format(old('down_payment', 0), 2, ',', '.') }}"
                    onfocus="this.select(); this.setSelectionRange(0, this.value.length);">
            </div>

            <div class="form-group mb-4">
                <label for="vat" class="block text-sm font-medium text-gray-700">PPN (%)</label>
                <input type="text" name="vat" id="vat" class="form-input mt-1 block w-full" readonly
                    value="{{ old('vat', 0) }}%" onfocus="this.select(); this.setSelectionRange(0, this.value.length);">
            </div>

            <div class="form-group mb-4">
                <label for="grand_total" class="block text-sm font-medium text-gray-700">Total Keseluruhan</label>
                <input type="text" name="grand_total" id="grand_total" class="form-input mt-1 block w-full" readonly
                    value="Rp {{ number_format(old('grand_total', 0), 2, ',', '.') }}"
                    onfocus="this.select(); this.setSelectionRange(0, this.value.length);">
            </div>

            <div class="form-group mb-4">
                <label for="payment_type" class="block text-sm font-medium text-gray-700">Jenis Pembayaran</label>
                <input type="text" name="payment_type" id="payment_type" 
                       class="form-input mt-1 block w-full" 
                       readonly value="{{ old('payment_type') }}"
                      >
            </div>
        </div>


        <!-- Tombol Simpan -->
        <div class="text-center mt-6">
            <button type="submit" class="btn btn-primary hover:bg-blue-600 transition duration-300 ease-in-out">
                <span class="relative">Save</span>
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('invoice.index') }}"
                class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">
                &lt; Back to List
            </a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sales_order_id').on('change', function() {
            var salesOrderId = $(this).val();

            if (salesOrderId) {
                $.ajax({
                    url: `/sales-order-data/${salesOrderId}`,
                    type: 'GET',
                    success: function(data) {
                        console.log(data); // Debugging
                        if (data) {
                            // Update fields based on the sales order data
                            $('#subtotal').val(data.subtotal || 0);
                            $('#discount').val(data.discount || 0);
                            $('#down_payment').val(data.down_payment || 0);
                            $('#vat').val(data.vat || 0);
                            $('#grand_total').val(data.grand_total || 0);
                            $('#payment_type').val(data.payment_type || ''); 

                            // Generate items HTML
                            const itemsHtml = Array.isArray(data.items) && data.items.length > 0
                                ? data.items.map(item => {
                                    const qtyShipped = item.quantity < 0 ? Math.abs(item.quantity) : item.quantity_shipped || 0;
                                    const qty = item.quantity < 0 ? 0 : item.quantity;
                                    const status = qty === 0 && qtyShipped > 0 && qtyShipped === item.quantity_total
                                        ? 'Sudah Terkirim'
                                        : item.status || 'N/A';

                                    return `
                                        <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mb-4">
                                            <h6 class="text-lg font-semibold text-primary">Item: ${item.item_name}</h6>
                                            <p><strong>Qty:</strong> ${qty}</p>
                                            <p><strong>Qty Shipped:</strong> ${qtyShipped}</p>
                                            <p><strong>Status:</strong> ${status}</p>
                                        </div>
                                    `;
                                }).join('') : '<p>No items found.</p>';

                            // Generate shipments HTML
                            const shipmentsHtml = Array.isArray(data.shipments) && data.shipments.length > 0
                                ? data.shipments.map(shipment => `
                                    <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mb-4">
                                        <h6 class="text-lg font-semibold text-primary">No Surat Jalan: ${shipment.surat_jalan_number || 'N/A'}</h6>
                                        <p><strong>Tanggal Kirim:</strong> ${shipment.shipping_date || 'N/A'}</p>
                                        <p><strong>Alamat Pengiriman:</strong> ${shipment.delivery_address || 'N/A'}</p>
                                    </div>
                                `).join('') : '<p>No shipments found.</p>';

                            // Generate details HTML
                            const detailsHtml = `
                                <div class="card mb-4 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Customer Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Nama Customer:</strong> ${data.customer_name || 'N/A'}</p>
                                        <p><strong>Alamat Customer:</strong> ${data.customer_address || 'N/A'}</p>
                                        <p><strong>Tanggal PO Customer:</strong> ${data.po_date || 'N/A'}</p>
                                        <p><strong>No PO Customer:</strong> ${data.po_number || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="card mb-4 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Surat Jalan Details</h5>
                                    </div>
                                    <div class="card-body">
                                        ${itemsHtml}
                                    </div>
                                </div>
                                <div class="card mb-4 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Shipment Schedule</h5>
                                    </div>
                                    <div class="card-body">
                                        ${shipmentsHtml}
                                    </div>
                                </div>
                            `;

                            $('#sales-order-details').html(detailsHtml);
                        } else {
                            $('#sales-order-details').html('<p>No details available.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching sales order data:', status, error);
                        $('#sales-order-details').html('<p class="text-red-500">Error fetching details.</p>');
                    }
                });
            } else {
                $('#sales-order-details').html('<p>Please select a sales order.</p>');
            }
        });
    });
</script>
@endsection