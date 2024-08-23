@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Edit Sales Order</h1>

    <form action="{{ route('salesOrders.update', $salesOrder) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $salesOrder->customer_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required>
            @error('customer_name')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="customer_address" class="block text-sm font-medium text-gray-700">Customer Address</label>
            <textarea id="customer_address" name="customer_address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required>{{ old('customer_address', $salesOrder->customer_address) }}</textarea>
            @error('customer_address')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="po_date" class="block text-sm font-medium text-gray-700">PO Date</label>
            <input type="date" id="po_date" name="po_date" value="{{ old('po_date', $salesOrder->po_date->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required>
            @error('po_date')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="po_number" class="block text-sm font-medium text-gray-700">PO Number</label>
            <input type="text" id="po_number" name="po_number" value="{{ old('po_number', $salesOrder->po_number) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required>
            @error('po_number')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="so_number" class="block text-sm font-medium text-gray-700">SO Number (Optional)</label>
            <input type="text" id="so_number" name="so_number" value="{{ old('so_number', $salesOrder->so_number) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
            @error('so_number')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

         <!-- Item Details -->
         <div class="mb-4">
            <h2 class="text-xl font-semibold mb-2">Item Details</h2>
            <div id="items-container">
                <!-- Items will be dynamically added here -->
            </div>
            <button type="button" id="add-item" class="btn btn-secondary">Add Item</button>
        </div>

        <div class="mb-4">
            <label for="discount" class="block text-sm font-medium text-gray-700">Diskon</label>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <input type="number" id="discount" name="discount" value="{{ old('discount', $salesOrder->discount) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01">
                </div>
                <div>
                    <select id="discount_type" name="discount_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="percent">%</option>
                        <option value="currency">IDR</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="down_payment" class="block text-sm font-medium text-gray-700">Down Payment</label>
            <input type="number" id="down_payment" name="down_payment" value="{{ old('down_payment', $salesOrder->down_payment) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" step="0.01" required>
            @error('down_payment')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="vat" class="block text-sm font-medium text-gray-700">VAT</label>
            <input type="number" id="vat" name="vat" value="{{ old('vat', $salesOrder->vat) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" step="0.01" required>
            @error('vat')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <div class="card p-4 border rounded-md shadow-sm">
                <h3 class="text-lg font-semibold mb-2">Summary</h3>
                <div id="summary-details">
                    <!-- Summary will be dynamically filled -->
                </div>
            </div>
        </div>
        <div class="mb-4">
            <label for="payment_schedule_type" class="block text-sm font-medium text-gray-700">Payment Schedule Type</label>
            <input type="text" id="payment_schedule_type" name="payment_schedule_type" value="{{ old('payment_schedule_type', $salesOrder->payment_schedule_type) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required>
            @error('payment_schedule_type')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemOptions = {
            "Paku": 500000,
            "Baja": 2000000,
            "Besi Panjang": 2500000
        };

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
                row.querySelector('.item-total').value = formatCurrency(total);
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
                <p>Sub Total: <span id="sub_total"">${formatCurrency(subTotal)}</span></p>
                <p>Discount: <span id="discount_amount" }}">${formatCurrency(discountAmount)}</span></p>
                <p>VAT: <span id="vat_amount">${formatCurrency(vatAmount)}</span></p>
                <p>Down Payment: <span id="down_payment_amount">${formatCurrency(downPayment)}</span></p>
                <p class="font-semibold">Grand Total: <span id="grand_total">${formatCurrency(grandTotal)}</span></p>
            `;
        }

        function addItemRow() {
            const itemCount = itemsContainer.children.length + 1;
            const itemRow = document.createElement('div');
            itemRow.classList.add('item-row', 'mb-4');
            itemRow.innerHTML = `
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label for="item_name_${itemCount}" class="block text-sm font-medium text-gray-700" ">Item Name</label>
                        <select id="item_name_${itemCount}" name="item_name[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-name">
                            <option value="">Select an item</option>
                            ${Object.keys(itemOptions).map(item => `<option value="${item}" data-price="${itemOptions[item]}">${item}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label for="item_qty_${itemCount}" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" id="item_qty_${itemCount}" name="item_qty[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-qty" step="0.01" required>
                    </div>
                    <div>
                        <label for="item_price_${itemCount}" class="block text-sm font-medium text-gray-700">Price (IDR)</label>
                        <input type="number" id="item_price_${itemCount}" name="item_price[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-price" step="0.01" required>
                    </div>
                    <div>
                        <label for="item_total_${itemCount}" class="block text-sm font-medium text-gray-700">Total (IDR)</label>
                        <input type="text" id="item_total_${itemCount}" name="item_total[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-total" readonly>
                    </div>
                </div>
            `;

            itemsContainer.appendChild(itemRow);

            const itemNameSelect = itemRow.querySelector('.item-name');
            const itemPriceInput = itemRow.querySelector('.item-price');
            const itemQtyInput = itemRow.querySelector('.item-qty');

            itemNameSelect.addEventListener('change', function () {
                const selectedItem = itemOptions[this.value] || 0;
                itemPriceInput.value = selectedItem;
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
