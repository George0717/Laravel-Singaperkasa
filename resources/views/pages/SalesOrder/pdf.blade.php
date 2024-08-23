@extends('layouts.pdf')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; color: #4CAF50; font-weight: bold;">Sales Order</h1>
        <p style="font-size: 18px; color: #333;"><strong>Order Number:</strong> {{ $salesOrder->so_number }}</p>
    </div>
    <!-- Customer Info Section -->
    <div style="margin-bottom: 30px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Customer Name:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $salesOrder->customer_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Customer Address:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $salesOrder->customer_address }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>PO Number:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $salesOrder->po_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>PO Date:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $salesOrder->po_date->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Order Details Section -->
    <div style="margin-top: 40px;">
        <h3 style="text-align: center; color: #4CAF50; margin-bottom: 20px;">Order Details</h3>
        @if($salesOrder->details && $salesOrder->details->count() > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Item Name</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Quantity</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Price</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesOrder->details as $detail)
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $detail->item_name }}</td>
                            <td style="padding: 10px; text-align: center; border-bottom: 1px solid #ddd;">{{ $detail->quantity }}</td>
                            <td style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;">{{ number_format($detail->price, 2) }}</td>
                            <td style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;">{{ number_format($detail->quantity * $detail->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #FF0000;">No order details found.</p>
        @endif
    </div>

    <!-- Footer Section -->
    <div style="margin-top: 40px; text-align: center;">
        <p style="font-size: 12px; color: #888;">Thank you for your business!</p>
    </div>
</div>
@endsection
