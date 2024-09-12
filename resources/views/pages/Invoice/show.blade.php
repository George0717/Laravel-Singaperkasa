@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Invoice Details</h1>
    <table class="table table-bordered">
        <tr>
            <th>Invoice Number</th>
            <td>{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <th>Customer</th>
            <td>{{ $invoice->salesOrder->customer_name }}</td>
        </tr>
        <tr>
            <th>Subtotal</th>
            <td>{{ $invoice->subtotal }}</td>
        </tr>
        <tr>
            <th>Discount</th>
            <td>{{ $invoice->discount }}</td>
        </tr>
        <tr>
            <th>Down Payment</th>
            <td>{{ $invoice->down_payment }}</td>
        </tr>
        <tr>
            <th>VAT</th>
            <td>{{ $invoice->vat }}</td>
        </tr>
        <tr>
            <th>Grand Total</th>
            <td>{{ $invoice->grand_total }}</td>
        </tr>
        <tr>
            <th>Payment Schedule Type</th>
            <td>{{ $invoice->payment_schedule_type }}</td>
        </tr>
    </table>
    <a href="{{ route('invoice.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
