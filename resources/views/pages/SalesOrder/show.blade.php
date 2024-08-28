@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-semibold mb-6 text-gray-800">Sales Order Details</h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <table class="table-auto w-full border-collapse mb-6">
                <tbody>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>Customer Name:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->customer_name }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>Customer Address:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->customer_address }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>PO Date:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->po_date->translatedFormat('j F Y') }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>PO Number:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->po_number }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>SO Number:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->so_number }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>Discount:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ number_format($salesOrder->discount, 0, ',', '.') }} {{ $salesOrder->discount_type }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>Down Payment:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ 'Rp ' . number_format($salesOrder->down_payment, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>Pajak:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ number_format($salesOrder->vat, 0, ',', '.') }} %</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>Grand Total:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ 'Rp ' . number_format($salesOrder->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>Tipe Pembayaran:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">{{ $salesOrder->payment_type }}</td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-700 border-b"><strong>PO Photo:</strong></td>
                        <td class="px-4 py-2 text-gray-800 border-b">
                            @if($salesOrder->po_photo)
                                <img src="{{ asset('storage/' . $salesOrder->po_photo) }}" alt="PO Photo" class="max-w-xs rounded-lg shadow-md">
                            @else
                                <span>No photo available</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Section for item details --}}
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Item Details</h2>
            <table class="table-auto w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 text-left text-gray-700">Item Name</th>
                        <th class="px-4 py-2 text-left text-gray-700">Quantity</th>
                        <th class="px-4 py-2 text-left text-gray-700">Price</th>
                        <th class="px-4 py-2 text-left text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesOrder->details as $detail)
                        <tr class="hover:bg-gray-100">
                            <td class="px-4 py-2 text-gray-700 border-b">{{ $detail->item_name }}</td>
                            <td class="px-4 py-2 text-gray-700 border-b">{{ $detail->quantity }}</td>
                            <td class="px-4 py-2 text-gray-700 border-b">{{ 'Rp ' . number_format($detail->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-gray-700 border-b">{{ 'Rp ' . number_format($detail->quantity * $detail->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6">
                <a href="{{ route('SalesOrders.index') }}" class="btn btn-secondary bg-gray-700 text-white hover:bg-gray-900">Back to List</a>
                <a href="{{ route('SalesOrders.printPDF', $salesOrder->id) }}" class="btn btn-primary bg-blue-700 text-white hover:bg-blue-900 px-4 py-2 rounded">Print PDF</a>
            </div>
        </div>
    </div>
@endsection
