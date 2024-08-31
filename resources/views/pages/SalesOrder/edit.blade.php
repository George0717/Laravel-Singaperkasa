@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex flex-wrap -mx-4">
        <!-- Form Column -->
        <div class="w-full lg:w-2/3 px-4">
            <form action="{{ route('salesOrders.update', $salesOrder->id) }}" method="POST" enctype="multipart/form-data"> @csrf
                @method('put')

                <div class="mb-4">
                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name"
                        value="{{ old('customer_name', $salesOrder->customer_name) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="customer_address" class="block text-sm font-medium text-gray-700">Customer
                        Address</label>
                    <textarea id="customer_address" name="customer_address"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        required>{{ old('customer_address', $salesOrder->customer_address) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="po_date" class="block text-sm font-medium text-gray-700">PO Date</label>
                    <input type="date" id="po_date" name="po_date"
                        value="{{ old('po_date', $salesOrder->po_date->format('Y-m-d')) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="po_number" class="block text-sm font-medium text-gray-700">PO Number</label>
                    <input type="text" id="po_number" name="po_number"
                        value="{{ old('po_number', $salesOrder->po_number) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="so_number" class="block text-sm font-medium text-gray-700">SO Number</label>
                    <input type="text" id="so_number" name="so_number"
                        value="{{ old('so_number', $salesOrder->so_number) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="discount" class="block text-sm font-medium text-gray-700">Discount</label>
                    <input type="text" id="discount" name="discount"
                        value="{{ old('discount', $salesOrder->discount) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="mb-4">
                    <label for="discount_type" class="block text-sm font-medium text-gray-700">Discount Type</label>
                    <select id="discount_type" name="discount_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="percent" {{ old('discount_type', $salesOrder->discount_type) == 'percent' ?
                            'selected' : '' }}>Percent</option>
                        <option value="amount" {{ old('discount_type', $salesOrder->discount_type) == 'amount' ?
                            'selected' : '' }}>Amount</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="vat" class="block text-sm font-medium text-gray-700">Pajak (VAT)</label>
                    <input type="text" id="vat" name="vat" value="{{ old('vat', $salesOrder->vat) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="mb-4">
                    <label for="down_payment" class="block text-sm font-medium text-gray-700">Down Payment</label>
                    <input type="text" id="down_payment" name="down_payment"
                        value="{{ old('down_payment', $salesOrder->down_payment) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>


                <!-- Item Details -->
                <div class="mb-4">
                    <h2 class="text-xl font-semibold mb-2">Item Details</h2>
                    <div id="items-container">
                        @foreach($salesOrder->details as $detail)
                        <div class="item-row mb-4 flex items-center gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Item Name</label>
                                <select name="item_name[]"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-name">
                                    <option value="">Select an item</option>
                                    @foreach($itemOptions as $item => $price)
                                    <option value="{{ $item }}" data-price="{{ $price }}" {{ $item==$detail->item_name ?
                                        'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" name="item_qty[]"
                                    class="item-qty mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    value="{{ $detail->quantity }}" required>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Price</label>
                                <input type="number" name="item_price[]"
                                    class="item-price mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    value="{{ $detail->price }}" required>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Total</label>
                                <input type="text"
                                    class="item-total mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
                            </div>
                            <button type="button" class="btn btn-danger remove-item">Remove</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-item"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Add Item</button>
                </div>
                <div class="mb-4">
                    <h2 class="text-xl font-semibold mb-2">Item Details Summary</h2>
                    <div id="summary-details" class="p-4 bg-white rounded-lg shadow-md">
                        <!-- Summary details will be populated by JavaScript -->
                    </div>
                </div>
                <div class="mb-4">
                    <label for="payment_type" class="block text-sm font-medium text-gray-700">Jenis Pembayaran</label>
                    <select id="payment_type" name="payment_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="{{ old('payment_type', $salesOrder->payment_type) }}">Select Payment Type</option>
                        <option value="{{ old('payment_type', $salesOrder->payment_type) }}">Cash</option>
                        <option value="{{ old('payment_type', $salesOrder->payment_type) }}">Credit</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="po_photo" class="block text-sm font-medium text-gray-700">PO Photo</label>
                    <input type="file" id="po_photo" name="po_photo"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                     @if($salesOrder->po_photo)
                        <img src="{{ asset('storage/' . $salesOrder->po_photo) }}" alt="PO Photo" class="max-w-xs rounded-lg shadow-md">
                    @else
                        <span>No photo available</span>
                    @endif
                </div>

                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Update
                        Sales Order</button>
            </form>
        </div>

        <!-- Minimap Column -->
        <div class="w-full lg:w-1/3 px-5">
            <div
                class="sticky top-0 bg-white p-6 rounded-lg shadow-lg transition-transform duration-300 transform hover:scale-105">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Data Sebelumnya</h2>
                <table class="table-auto w-full border-collapse">
                    <tbody>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>Customer Name:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->customer_name }}</td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>Customer Address:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->customer_address }}</td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>PO Date:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->po_date->format('d M Y') }}
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>PO Number:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->po_number }}</td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>SO Number:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->so_number }}</td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>Discount:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->discount }}</td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>Discount Type:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->discount_type }}</td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>VAT:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->vat }}</td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>Down Payment:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->down_payment }}</td>
                        </tr>
                        <tr class="hover:bg-gray-100 transition-colors duration-300">
                            <td class="px-4 py-2 text-gray-700 border-b"><strong>Grand Total:</strong></td>
                            <td class="px-4 py-2 text-gray-800 border-b">{{ 'Rp ' . number_format($salesOrder->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
   document.addEventListener('DOMContentLoaded', function () {
    const itemOptions = {
        "Paku": 500000,
        "Baja": 2000000,
        "Besi Panjang": 2500000
    };

    let itemCount = {{ count($salesOrder->details) }};

    const itemsContainer = document.getElementById('items-container');
    const discountInput = document.getElementById('discount');
    const discountTypeSelect = document.getElementById('discount_type');
    const vatInput = document.getElementById('vat');
    const downPaymentInput = document.getElementById('down_payment');
    const summaryDetails = document.getElementById('summary-details');

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
    }

    function updateTotal() {
        const rows = document.querySelectorAll('#items-container .item-row');
        let subTotal = 0;

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const total = qty * price;
            row.querySelector('.item-total').value = formatCurrency(total);
            subTotal += total;
        });

        const discount = parseFloat(discountInput.value) || 0;
        const discountType = discountTypeSelect.value;
        const vat = parseFloat(vatInput.value) || 0;
        const downPayment = parseFloat(downPaymentInput.value) || 0;

        let discountAmount = 0;
        if (discountType === 'percent') {
            discountAmount = (subTotal * discount) / 100;
        } else if (discountType === 'amount') {
            discountAmount = discount;
        }

        const vatAmount = (subTotal * vat) / 100;
        const grandTotal = (subTotal + vatAmount) - discountAmount - downPayment;

        summaryDetails.innerHTML = `
            <p>Sub Total: <span id="sub_total">${formatCurrency(subTotal)}</span></p>
            <p>Discount: <span id="discount_amount">${formatCurrency(discountAmount)}</span></p>
            <p>VAT: <span id="vat_amount">${formatCurrency(vatAmount)}</span></p>
            <p>Down Payment: <span id="down_payment_amount">${formatCurrency(downPayment)}</span></p>
            <p class="font-semibold">Grand Total: <span id="grand_total">${formatCurrency(grandTotal)}</span></p>
        `;
    }

    function handleItemChange(event) {
        const select = event.target;
        const priceInput = select.closest('.item-row').querySelector('.item-price');
        const selectedOption = select.options[select.selectedIndex];
        priceInput.value = selectedOption.dataset.price || 0;
        updateTotal();
    }

    document.getElementById('add-item').addEventListener('click', function () {
        itemCount++;
        const container = document.getElementById('items-container');
        const newItem = `
            <div class="item-row mb-4 flex items-center gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Item Name</label>
                    <select name="item_name[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm item-name" onchange="handleItemChange(event)">
                        <option value="">Select an item</option>
                        ${Object.keys(itemOptions).map(item => `<option value="${item}" data-price="${itemOptions[item]}">${item}</option>`).join('')}
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="item_qty[]" class="item-qty mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="item_price[]" class="item-price mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Total</label>
                    <input type="text" class="item-total mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
                </div>
                <button type="button" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 remove-item">Remove</button>
            </div>`;
        container.insertAdjacentHTML('beforeend', newItem);
        container.querySelectorAll('.item-qty').forEach(input => input.addEventListener('input', updateTotal));
        container.querySelectorAll('.item-price').forEach(input => input.addEventListener('input', updateTotal));
        container.querySelectorAll('.item-name').forEach(select => select.addEventListener('change', handleItemChange));
        updateTotal();
    });

    document.querySelectorAll('.item-qty').forEach(input => input.addEventListener('input', updateTotal));
    document.querySelectorAll('.item-price').forEach(input => input.addEventListener('input', updateTotal));
    document.querySelectorAll('.item-name').forEach(select => select.addEventListener('change', handleItemChange));
    discountInput.addEventListener('input', updateTotal);
    discountTypeSelect.addEventListener('change', updateTotal);
    vatInput.addEventListener('input', updateTotal);
    downPaymentInput.addEventListener('input', updateTotal);

    // Initial update
    updateTotal();
});




</script>
@endsection