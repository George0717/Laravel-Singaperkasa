@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <form action="{{ route('salesOrders.update', $salesOrder->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $salesOrder->customer_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="customer_address" class="block text-sm font-medium text-gray-700">Customer Address</label>
                <textarea id="customer_address" name="customer_address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('customer_address', $salesOrder->customer_address) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="po_date" class="block text-sm font-medium text-gray-700">PO Date</label>
                <input type="date" id="po_date" name="po_date" value="{{ old('po_date', $salesOrder->po_date->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="po_number" class="block text-sm font-medium text-gray-700">PO Number</label>
                <input type="text" id="po_number" name="po_number" value="{{ old('po_number', $salesOrder->po_number) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="discount" class="block text-sm font-medium text-gray-700">Discount</label>
                <input type="number" id="discount" name="discount" value="{{ old('discount', $salesOrder->discount) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div class="mb-4">
                <label for="discount_type" class="block text-sm font-medium text-gray-700">Discount Type</label>
                <select id="discount_type" name="discount_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="percent" {{ old('discount_type', $salesOrder->discount_type) == 'percent' ? 'selected' : '' }}>Percent</option>
                    <option value="currency" {{ old('discount_type', $salesOrder->discount_type) == 'currency' ? 'selected' : '' }}>Currency</option>
                </select>
            </div>

            <!-- Similar fields for VAT, Down Payment, Payment Type, Due Date, etc. -->

            <div id="items-container">
                @foreach($salesOrder->items ?? [] as $index => $item)
                    <div class="item-row mb-4 flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Item Name</label>
                            <input type="text" name="item_name[]" value="{{ old('item_name.' . $index, $item->item_name) }}" class="item-name mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" name="item_qty[]" value="{{ old('item_qty.' . $index, $item->item_qty) }}" class="item-qty mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="number" name="item_price[]" value="{{ old('item_price.' . $index, $item->item_price) }}" class="item-price mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Total</label>
                            <input type="text" class="item-total mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
                        </div>
                        <button type="button" class="btn btn-danger remove-item" onclick="this.parentElement.remove()">Remove</button>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-item" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Add Item</button>

            <div class="mb-4">
                <label for="po_photo" class="block text-sm font-medium text-gray-700">PO Photo</label>
                <input type="file" id="po_photo" name="po_photo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @if($salesOrder->po_photo)
                    <img src="{{ Storage::url('public/sales_orders/' . $salesOrder->po_photo) }}" alt="PO Photo" class="mt-2">
                @endif
            </div>

            <div class="mb-4">
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Update Sales Order</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateSummary() {
                const rows = document.querySelectorAll('.item-row');
                rows.forEach(row => {
                    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                    const price = parseFloat(row.querySelector('.item-price').value) || 0;
                    const total = qty * price;
                    row.querySelector('.item-total').value = total.toFixed(2);
                });
            }

            document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const index = container.children.length; // New index for added item
        const newItem = `
            <div class="item-row mb-4 flex items-center gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input type="text" name="item_name[]" class="item-name mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="item_qty[]" class="item-qty mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="item_price[]" class="item-price mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Total</label>
                    <input type="text" class="item-total mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
                </div>
                <button type="button" class="btn btn-danger remove-item" onclick="this.parentElement.remove()">Remove</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newItem);
    });


                itemsContainer.appendChild(itemRow);

                itemRow.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', updateSummary);
                });

                updateSummary();
            });

            document.querySelectorAll('.item-row input').forEach(input => {
                input.addEventListener('input', updateSummary);
            });

            // Initialize with existing items
            document.querySelectorAll('.item-row').forEach(row => {
                row.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', updateSummary);
                });
            });

            updateSummary();
        });
    </script>
@endsection
