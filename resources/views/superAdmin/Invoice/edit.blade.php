@extends('layouts.superAdmin')

@section('title', 'Edit Invoice')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-8 text-center text-primary">Edit Invoice</h1>

    <!-- Pesan Sukses -->
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Form untuk mengedit invoice -->
    <form action="{{ route('superAdmin.invoice.update', $invoice->id) }}" method="POST" class="bg-white p-8 shadow-lg rounded-lg space-y-6">
        @csrf
        @method('PUT')

        <!-- Nomor Invoice -->
        <div class="mb-4">
            <label for="invoice_number" class="block text-sm font-medium text-gray-700">Invoice Number</label>
            <input type="text" id="invoice_number" name="invoice_number" value="{{ $invoice->invoice_number }}" readonly
                class="form-input mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md p-2">
            @error('invoice_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pemilihan Sales Order -->
        <div class="mb-4">
            <label for="sales_order_id" class="block text-sm font-medium text-gray-700">Sales Order</label>
            <select id="sales_order_id" name="sales_order_id" class="form-select mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                <option value="" disabled>Select Sales Order</option>
                @foreach ($salesOrders as $order)
                <option value="{{ $order->id }}" {{ $invoice->sales_order_id == $order->id ? 'selected' : '' }}>
                    {{ $order->so_number }} - {{ $order->customer_name }}
                </option>
                @endforeach
            </select>
            @error('sales_order_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Detail Sales Order akan dimuat di sini secara dinamis -->
        <div id="sales-order-details" class="mt-6">
            <!-- Konten akan dimasukkan di sini secara dinamis -->
        </div>

        <!-- Detail Invoice -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            <div class="mb-4">
                <label for="subtotal" class="block text-sm font-medium text-gray-700">Subtotal</label>
                <input type="text" name="subtotal" id="subtotal" class="form-input mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md p-2" readonly
                    value="Rp {{ number_format($invoice->subtotal, 2, ',', '.') }}">
            </div>

            <div class="mb-4">
                <label for="discount" class="block text-sm font-medium text-gray-700">Diskon</label>
                <input type="text" name="discount" id="discount" class="form-input mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md p-2" readonly
                    value="Rp {{ number_format($invoice->discount, 2, ',', '.') }}">
            </div>

            <div class="mb-4">
                <label for="down_payment" class="block text-sm font-medium text-gray-700">Uang Muka</label>
                <input type="text" name="down_payment" id="down_payment" class="form-input mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md p-2" readonly
                    value="Rp {{ number_format($invoice->down_payment, 2, ',', '.') }}">
            </div>

            <div class="mb-4">
                <label for="vat" class="block text-sm font-medium text-gray-700">PPN (%)</label>
                <input type="text" name="vat" id="vat" class="form-input mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md p-2" readonly
                    value="{{ $invoice->vat }}%">
            </div>

            <div class="mb-4">
                <label for="grand_total" class="block text-sm font-medium text-gray-700">Total Keseluruhan</label>
                <input type="text" name="grand_total" id="grand_total" class="form-input mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md p-2" readonly
                    value="Rp {{ number_format($invoice->grand_total, 2, ',', '.') }}">
            </div>

            <div class="mb-4">
                <label for="payment_type" class="block text-sm font-medium text-gray-700">Jenis Pembayaran</label>
                <input type="text" name="payment_type" id="payment_type" class="form-input mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md p-2" readonly
                    value="{{ $invoice->payment_type }}">
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="text-center mt-6">
            <button type="submit" class="bg-primary text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition ease-in-out duration-300">
                Update
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('superAdmin.invoice.index') }}"
                class="text-blue-600 hover:text-blue-900 transition ease-in-out duration-300">
                &lt; Back to List
            </a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Memuat detail Sales Order saat halaman diakses pertama kali
        var initialSalesOrderId = $('#sales_order_id').val();
        if (initialSalesOrderId) {
            loadSalesOrderDetails(initialSalesOrderId);
        }

        $('#sales_order_id').on('change', function() {
            var salesOrderId = $(this).val();
            if (salesOrderId) {
                loadSalesOrderDetails(salesOrderId);
            }
        });

        function loadSalesOrderDetails(salesOrderId) {
            $.ajax({
                url: `/sales-order-data/${salesOrderId}`,
                type: 'GET',
                success: function(data) {
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
                            ? data.items.map(item => `
                                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-300 mb-4">
                                    <h6 class="text-lg font-semibold text-primary">Item: ${item.item_name}</h6>
                                    <p><strong>Qty:</strong> ${item.quantity}</p>
                                    <p><strong>Qty Shipped:</strong> ${item.quantity_shipped || 0}</p>
                                    <p><strong>Status:</strong> ${item.status || 'N/A'}</p>
                                </div>
                            `).join('') : '<p>No items found.</p>';

                        // Generate shipments HTML
                        const shipmentsHtml = Array.isArray(data.shipments) && data.shipments.length > 0
                            ? data.shipments.map(shipment => `
                                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-300 mb-4">
                                    <h6 class="text-lg font-semibold text-primary">No Surat Jalan: ${shipment.no_surat_jalan || 'N/A'}</h6>
                                    <p><strong>Tanggal Kirim:</strong> ${new Date(shipment.tanggal_pengiriman).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) || 'N/A'}</p>
                                    <p><strong>Plat Angkutan:</strong> ${shipment.plat_angkutan || 'N/A'}</p>
                                    <p><strong>Jumlah:</strong> ${shipment.jumlah || 'N/A'}</p>
                                </div>
                            `).join('') : '<p>Tidak ada pengiriman</p>';

                        // Generate details HTML
                        const detailsHtml = `
                            <div class="card mb-4 border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Customer Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nama Customer:</strong> ${data.customer_name || 'N/A'}</p>
                                    <p><strong>Alamat Customer:</strong> ${data.customer_address || 'N/A'}</p>
                                    <p><strong>Tanggal PO Customer:</strong> ${new Date(data.po_date).toLocaleDateString('id-ID', { day: 'numeric',, month: 'long', year: 'numeric' }) || 'N/A'}</p>
                                    </div>
                                </div>
                                <h5 class="text-lg font-semibold text-primary mt-6">Items</h5>
                                ${itemsHtml}
                                <h5 class="text-lg font-semibold text-primary mt-6">Pengiriman</h5>
                                ${shipmentsHtml}
                            `;
                            $('#sales-order-details').html(detailsHtml);
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
