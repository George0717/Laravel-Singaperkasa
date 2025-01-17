@extends('layouts.superAdmin')
@section('title', 'Buat Sales Order')
@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Buat Sales Order</h1>
    <a href="{{ route('superAdmin.SalesOrders.index') }}" class="inline-block mb-4 text-blue-600 hover:text-blue-800">
        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md shadow-sm">
            &larr; Back to Sales Orders
        </button>
    </a>

    <form action="{{ route('superAdmin.salesOrders.store') }}" method="POST" id="sales-order-form"
        enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label for="customer_name" class="block text-sm font-medium text-gray-700">Nama Customer</label>
            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
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
                <!-- Items will be dynamically added here -->
            </div>
            <button type="button" id="add-item" class="btn btn-secondary">Tambah Barang</button>
        </div>

        <!-- Additional Fields -->
        <div class="mb-4">
            <label for="discount" class="block text-sm font-medium text-gray-700">Diskon</label>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <input type="number" id="discount" name="discount" value="{{ old('discount', 0) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01">
                </div>
                <div>
                    <select id="discount_type" name="discount_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="percent">%</option>
                        <option value="currency">IDR</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="vat" class="block text-sm font-medium text-gray-700">Pajak (%)</label>
            <input type="number" id="vat" name="vat" value="{{ old('vat', 0) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01">
        </div>

        <div class="mb-4">
            <label for="down_payment" class="block text-sm font-medium text-gray-700">Uang Muka</label>
            <input type="number" id="down_payment" name="down_payment" value="{{ old('down_payment', 0) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01">
        </div>

        <!-- Calculations Card -->
        <div class="mb-4">
            <div class="card p-4 border rounded-md shadow-sm">
                <h3 class="text-lg font-semibold mb-2">Summary</h3>
                <div id="summary-details">
                    <!-- Summary will be dynamically filled -->
                </div>
                <input type="hidden" id="grand_total_hidden" name="grand_total" value="{{ old('grand_total') }}">

            </div>
        </div>

        <!-- Payment Details -->
        <div class="mb-4">
            <label for="payment_type" class="block text-sm font-medium text-gray-700">Jenis Pembayaran</label>
            <select id="payment_type" name="payment_type"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Select Payment Type</option>
                <option value="Cash">Cash</option>
                <option value="Credit">Credit</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="due_date" class="block text-sm font-medium text-gray-700">Jatuh Tempo Pembayaran</label>
            <input type="date" id="due_date" name="due_date"
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
        <button type="submit" class="btn btn-primary">Save</button>
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
                const vat = parseFloat(document.getElementById('vat').value) || 0;
                const downPayment = parseFloat(document.getElementById('down_payment').value) || 0;

                let discountAmount = 0;
                if (discountType === 'percent') {
                    discountAmount = (subTotal * discount) / 100;
                } else if (discountType === 'currency') {
                    discountAmount = discount;
                }

                const vatAmount = (subTotal * vat) / 100;
                const grandTotal = (subTotal + vatAmount) - discountAmount - downPayment;

                summaryDetails.innerHTML = `
                    <p>Sub Total: <span id="sub_total">${formatCurrency(subTotal)}</span></p>
                    <p>Discount: <span id="discount_amount">${formatCurrency(discountAmount)}</span></p>
                    <p>VAT: <span id="vat_amount">${formatCurrency(vatAmount)}</span></p>
                    <p>Down Payment: <span id="down_payment_amount">${formatCurrency(downPayment)}</span></p>
                    <p class="font-semibold">Grand Total: <span id="grand_total" name="grand_total">${formatCurrency(grandTotal)}</span></p>
                `;
                document.getElementById('grand_total_hidden').value = grandTotal;
            }

            function addItemRow() {
    const itemCount = itemsContainer.children.length + 1;
    const itemRow = document.createElement('div');
    itemRow.classList.add('item-row', 'mb-4');
    itemRow.innerHTML = `
    <div class="grid grid-cols-1 gap-4">
    <div class="flex items-center space-x-4">
        <div class="flex-1">
            <label for="item_name_${itemCount}" class="block text-sm font-medium text-gray-700">Nama Barang</label>
            <select id="item_name_${itemCount}" name="items[${itemCount}][stock_barang_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-name">
                <option value="">Pilih Barang</option>
                ${stockBarangs.map(barang => 
                    `<option value="${barang.id}" data-stock="${barang.jumlah_barang}" data-harga="${barang.harga}">${barang.nama_barang} (${barang.tipe_barang ?? 'tidak ada tipe'}) </option>`
                ).join('')}
            </select>
        </div>
        <div class="w-16">
            <label for="item_qty_${itemCount}" class="block text-sm font-medium text-gray-700">Quantity</label>
            <input type="number" id="item_qty_${itemCount}" name="items[${itemCount}][jumlah_barang]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-qty" step="1">
        </div>
        <div class="flex-1">
            <label for="item_price_${itemCount}" class="block text-sm font-medium text-gray-700">Harga</label>
            <input type="number" id="item_price_${itemCount}" name="items[${itemCount}][price]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-price" step="1" required>
        </div>
        <div class="w-16">
            <label for="item_per_${itemCount}" class="block text-sm font-medium text-gray-700">Per</label>
            <input type="text" id="item_per_${itemCount}" name="items[${itemCount}][per]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-price text-center" maxlength="2" value="Kg" readonly>
        </div>
        <div class="flex-1">
            <label for="item_total_${itemCount}" class="block text-sm font-medium text-gray-700">Total</label>
            <input type="text" id="item_total_${itemCount}" name="items[${itemCount}][total]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-total" readonly>
        </div>
    </div>
</div>

    `;
    itemsContainer.appendChild(itemRow);

    const itemNameSelect = itemRow.querySelector('.item-name');
    const itemPriceInput = itemRow.querySelector('.item-price');
    const itemQtyInput = itemRow.querySelector('.item-qty');

    itemNameSelect.addEventListener('change', function () {
        const selectedItem = stockBarangs.find(barang => barang.id == this.value);
        if (selectedItem) {
            itemPriceInput.value = selectedItem.harga;  // Set harga berdasarkan barang yang dipilih
        }
        updateSummary();
    });

    itemQtyInput.addEventListener('input', updateSummary);
    itemPriceInput.addEventListener('input', updateSummary);
}

            document.getElementById('add-item').addEventListener('click', addItemRow);
            document.getElementById('discount').addEventListener('input', updateSummary);
            document.getElementById('vat').addEventListener('input', updateSummary);
            document.getElementById('down_payment').addEventListener('input', updateSummary);

            addItemRow(); // Add initial item row
        });
</script>
@endsection