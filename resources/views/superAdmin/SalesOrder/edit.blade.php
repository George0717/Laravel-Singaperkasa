@extends('layouts.superAdmin')
@section('title', 'Edit Sales Order')
@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Edit Sales Order</h1>
    <a href="{{ route('superAdmin.SalesOrders.index') }}" class="inline-block mb-4 text-blue-600 hover:text-blue-800">
        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md shadow-sm">
            &larr; Back to Sales Orders
        </button>
    </a>

    <form action="{{ route('superAdmin.salesOrders.update', $salesOrder->id) }}" method="POST" id="sales-order-form"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Customer Name -->
        <div class="mb-4">
            <label for="customer_name" class="block text-sm font-medium text-gray-700">Nama Customer</label>
            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $salesOrder->customer_name) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                required>
            @error('customer_name')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- File Upload -->
        <div class="mb-4">
            <label for="po_photo" class="block text-sm font-medium text-gray-700">Photo PO</label>
            <input type="file" id="po_photo" name="po_photo"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('po_photo')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Item Details -->
        <div class="mb-4">
            <h2 class="text-xl font-semibold mb-2">Item Details</h2>
            <div id="items-container">
                @foreach($salesOrder->details as $key => $item)
                <div class="item-row mb-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <label for="item_name_{{ $key }}" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                <select id="item_name_{{ $key }}" name="items[{{ $key }}][stock_barang_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-name">
                                    <option value="">Pilih Barang</option>
                                    @foreach($stockBarangs as $barang)
                                    <option value="{{ $barang->id }}" {{ $item->stock_barang_id == $barang->id ? 'selected' : '' }} data-stock="{{ $barang->jumlah_barang }}" data-harga="{{ $barang->harga }}">{{ $barang->nama_barang }} ({{ $barang->tipe_barang ?? 'tidak ada tipe' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-16">
                                <label for="item_qty_{{ $key }}" class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" id="item_qty_{{ $key }}" name="items[{{ $key }}][jumlah_barang]" value="{{ old('items.'.$key.'.jumlah_barang', $item->quantity) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-qty" step="1">
                            </div>
                            <div class="flex-1">
                                <label for="item_price_{{ $key }}" class="block text-sm font-medium text-gray-700">Harga</label>
                                <input type="number" id="item_price_{{ $key }}" name="items[{{ $key }}][price]" value="{{ old('items.'.$key.'.price', $item->price) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-price" step="1" required>
                            </div>
                            <div class="w-16">
                                <label for="item_per_{{ $key }}" class="block text-sm font-medium text-gray-700">Per</label>
                                <input type="text" id="item_per_{{ $key }}" name="items[{{ $key }}][per]" value="Kg"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-price text-center" maxlength="2" readonly>
                            </div>
                            <div class="flex-1">
                                <label for="item_total_{{ $key }}" class="block text-sm font-medium text-gray-700">Total</label>
                                <input type="text" id="item_total_{{ $key }}" name="items[{{ $key }}][total]" value="{{ old('items.'.$key.'.total', $item->total) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-total" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-item" class="bg-blue-600 text-white py-2 px-4 rounded-md">Tambah Barang</button>
        </div>

        <!-- Additional Fields -->
        <div class="mb-4">
            <label for="discount" class="block text-sm font-medium text-gray-700">Diskon</label>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <input type="number" id="discount" name="discount" value="{{ old('discount', $salesOrder->discount) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01">
                </div>
                <div>
                    <select id="discount_type" name="discount_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="percent" {{ $salesOrder->discount_type == 'percent' ? 'selected' : '' }}>%</option>
                        <option value="currency" {{ $salesOrder->discount_type == 'currency' ? 'selected' : '' }}>IDR</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="vat" class="block text-sm font-medium text-gray-700">Pajak (%)</label>
            <input type="number" id="vat" name="vat" value="{{ old('vat', $salesOrder->vat) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01">
        </div>

        <div class="mb-4">
            <label for="down_payment" class="block text-sm font-medium text-gray-700">Uang Muka</label>
            <input type="number" id="down_payment" name="down_payment" value="{{ old('down_payment', $salesOrder->down_payment) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01">
        </div>

        <!-- Calculations Card -->
        <div class="mb-4">
            <div class="card p-4 border rounded-md shadow-sm">
                <h3 class="text-lg font-semibold mb-2">Summary</h3>
                <div id="summary-details">
                    <!-- Summary will be dynamically filled -->
                </div>
                <input type="hidden" id="grand_total_hidden" name="grand_total" value="{{ old('grand_total', $salesOrder->grand_total) }}">
            </div>
        </div>

        <!-- Payment Details -->
        <div class="mb-4">
            <label for="payment_type" class="block text-sm font-medium text-gray-700">Jenis Pembayaran</label>
            <select id="payment_type" name="payment_type"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Select Payment Type</option>
                <option value="Cash" {{ $salesOrder->payment_type == 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="Credit" {{ $salesOrder->payment_type == 'Credit' ? 'selected' : '' }}>Credit</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="due_date" class="block text-sm font-medium text-gray-700">Jatuh Tempo Pembayaran</label>
            <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $salesOrder->due_date) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md">Update Sales Order</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stockBarangs = @json($stockBarangs); // Mengambil data barang dari controller
        const itemsContainer = document.getElementById('items-container');
        const summaryDetails = document.getElementById('summary-details');
        const discountInput = document.getElementById('discount');
        const discountTypeSelect = document.getElementById('discount_type');
        const vatInput = document.getElementById('vat');
        const downPaymentInput = document.getElementById('down_payment');

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
        }

        function updateSummary() {
            let subTotal = 0;
            const itemRows = itemsContainer.querySelectorAll('.item-row');
            itemRows.forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const total = qty * price;
                row.querySelector('.item-total').value = total;  // Hanya kirim angka
                subTotal += total;
            });

            const discount = parseFloat(discountInput.value) || 0;
            const discountType = discountTypeSelect.value;
            const vat = parseFloat(vatInput.value) || 0;
            const downPayment = parseFloat(downPaymentInput.value) || 0;

            let discountAmount = 0;
            if (discountType === 'percent') {
                discountAmount = (subTotal * discount) / 100;
            } else {
                discountAmount = discount;
            }

            const grandTotal = subTotal - discountAmount + (subTotal * vat) / 100 - downPayment;

            // Update UI
            summaryDetails.innerHTML = `
                <div class="flex justify-between">
                    <span class="font-medium">Subtotal</span>
                    <span>${formatCurrency(subTotal)}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Diskon</span>
                    <span>- ${formatCurrency(discountAmount)}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Pajak (VAT)</span>
                    <span>${formatCurrency((subTotal * vat) / 100)}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Uang Muka</span>
                    <span>- ${formatCurrency(downPayment)}</span>
                </div>
                <div class="flex justify-between font-semibold">
                    <span class="font-medium">Grand Total</span>
                    <span>${formatCurrency(grandTotal)}</span>
                </div>
            `;
            document.getElementById('grand_total_hidden').value = grandTotal;
        }

        // Event listeners for recalculating
        itemsContainer.addEventListener('input', updateSummary);
        discountInput.addEventListener('input', updateSummary);
        discountTypeSelect.addEventListener('change', updateSummary);
        vatInput.addEventListener('input', updateSummary);
        downPaymentInput.addEventListener('input', updateSummary);

        // Add new item row
        document.getElementById('add-item').addEventListener('click', function () {
            const itemIndex = itemsContainer.children.length;

            const newItem = document.createElement('div');
            newItem.classList.add('item-row', 'mb-4');
            newItem.innerHTML = `
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label for="item_name_${itemIndex}" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                            <select id="item_name_${itemIndex}" name="items[${itemIndex}][stock_barang_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-name">
                                <option value="">Pilih Barang</option>
                                @foreach($stockBarangs as $barang)
                                <option value="{{ $barang->id }}" data-stock="{{ $barang->jumlah_barang }}" data-harga="{{ $barang->harga }}">{{ $barang->nama_barang }} ({{ $barang->tipe_barang ?? 'tidak ada tipe' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-16">
                            <label for="item_qty_${itemIndex}" class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="item_qty_${itemIndex}" name="items[${itemIndex}][jumlah_barang]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-qty" step="1">
                        </div>
                        <div class="flex-1">
                            <label for="item_price_${itemIndex}" class="block text-sm font-medium text-gray-700">Harga</label>
                            <input type="number" id="item_price_${itemIndex}" name="items[${itemIndex}][price]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-price" step="1" required>
                        </div>
                        <div class="w-16">
                            <label for="item_per_${itemIndex}" class="block text-sm font-medium text-gray-700">Per</label>
                            <input type="text" id="item_per_${itemIndex}" name="items[${itemIndex}][per]" value="Kg" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-price text-center" maxlength="2" readonly>
                        </div>
                        <div class="flex-1">
                            <label for="item_total_${itemIndex}" class="block text-sm font-medium text-gray-700">Total</label>
                            <input type="text" id="item_total_${itemIndex}" name="items[${itemIndex}][total]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-total" readonly>
                        </div>
                    </div>
                </div>
            `;

            itemsContainer.appendChild(newItem);
            updateSummary();
        });

        updateSummary(); // Initialize calculation
    });
</script>
@endsection
