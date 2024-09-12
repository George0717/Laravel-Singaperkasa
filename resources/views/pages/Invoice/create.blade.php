@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Create Invoice</h1>
    
    <form action="{{ route('invoice.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="sales_order_id" class="form-label">Sales Order</label>
                <select name="sales_order_id" id="sales_order_id" class="form-select">
                    <option value="">Select Sales Order</option>
                    @foreach($salesOrders as $order)
                        <option value="{{ $order->id }}">{{ $order->so_number }} - {{ $order->customer_name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="sales-order-details" class="col-12">
                <!-- Customer Details, Delivery Schedule, and Surat Jalan will be loaded here -->
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="invoice_number" class="form-label">Invoice Number</label>
                <input type="text" name="invoice_number" id="invoice_number" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="subtotal" class="form-label">Subtotal</label>
                <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="discount" class="form-label">Discount</label>
                <input type="number" step="0.01" name="discount" id="discount" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="down_payment" class="form-label">Down Payment</label>
                <input type="number" step="0.01" name="down_payment" id="down_payment" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="vat" class="form-label">VAT</label>
                <input type="number" step="0.01" name="vat" id="vat" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="grand_total" class="form-label">Grand Total</label>
                <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="payment_schedule_type" class="form-label">Payment Schedule Type</label>
                <input type="text" name="payment_schedule_type" id="payment_schedule_type" class="form-control" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

<script>
    document.getElementById('sales_order_id').addEventListener('change', function() {
        var salesOrderId = this.value;

        if (salesOrderId) {
            fetch(`/sales-order-data/${salesOrderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        // Default values for null data
                        let factoryName = data.factory_name || 'N/A';
                        let factoryAddress = data.factory_address || 'N/A';
                        let address = data.address || 'N/A';
                        let phone = data.phone || 'N/A';

                        let itemsHtml = Array.isArray(data.items) ? data.items.map(item => {
                            let qtyShipped = item.quantity_shipped || 0;
                            return `
                                <div class="mb-3">
                                    <p><strong>Item:</strong> ${item.item_name}</p>
                                    <p><strong>Qty:</strong> ${item.quantity}</p>
                                    <p><strong>Qty Shipped:</strong> ${qtyShipped}</p>
                                    <p><strong>Status:</strong> ${item.status || 'N/A'}</p>
                                </div>
                            `;
                        }).join('') : '<p>No items found.</p>';

                        let shipmentsHtml = Array.isArray(data.shipments) ? data.shipments.map(shipment => `
                            <div class="mb-3">
                                <p><strong>No Surat Jalan:</strong> ${shipment.surat_jalan_number || 'N/A'}</p>
                                <p><strong>Tanggal Kirim:</strong> ${shipment.shipping_date || 'N/A'}</p>
                                <p><strong>Alamat Pengiriman:</strong> ${shipment.delivery_address || 'N/A'}</p>
                            </div>
                        `).join('') : '<p>No shipments found.</p>';

                        let detailsHtml = `
                            <div class="card my-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Customer Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nama Customer:</strong> ${data.customer_name || 'N/A'}</p>
                                    <p><strong>Alamat Customer:</strong> ${data.customer_address || 'N/A'}</p>
                                    <p><strong>Tanggal PO Customer:</strong> ${data.po_date || 'N/A'}</p>
                                    <p><strong>No PO Customer:</strong> ${data.po_number || 'N/A'}</p>
                                    <p><strong>Nama Pabrik:</strong> ${factoryName}</p>
                                    <p><strong>Alamat Pabrik:</strong> ${factoryAddress}</p>
                                    <p><strong>Alamat:</strong> ${address}</p>
                                    <p><strong>Telpon:</strong> ${phone}</p>
                                </div>
                            </div>
                            <div class="card my-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Surat Jalan Details</h5>
                                </div>
                                <div class="card-body">
                                    ${itemsHtml}
                                </div>
                            </div>
                            <div class="card my-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Shipment Schedule</h5>
                                </div>
                                <div class="card-body">
                                    ${shipmentsHtml}
                                </div>
                            </div>
                            <div class="card my-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Payment Schedule</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Jadwal Pembayaran:</strong> ${data.due_date || 'N/A'}</p>
                                </div>
                            </div>
                        `;

                        document.getElementById('sales-order-details').innerHTML = detailsHtml;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            document.getElementById('sales-order-details').innerHTML = '';
        }
    });
</script>
@endsection
