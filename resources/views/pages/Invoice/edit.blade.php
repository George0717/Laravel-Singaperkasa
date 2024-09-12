@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Invoice</h1>
    <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="sales_order_id">Sales Order</label>
            <select name="sales_order_id" id="sales_order_id" class="form-control">
                @foreach($salesOrders as $order)
                    <option value="{{ $order->id }}" {{ $order->id == $invoice->sales_order_id ? 'selected' : '' }}>
                        {{ $order->so_number }} - {{ $order->customer_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="invoice_number">Invoice Number</label>
            <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="{{ $invoice->invoice_number }}" required>
        </div>
        <div class="form-group">
            <label for="subtotal">Subtotal</label>
            <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control" value="{{ $invoice->subtotal }}" required>
        </div>
        <div class="form-group">
            <label for="discount">Discount</label>
            <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="{{ $invoice->discount }}">
        </div>
        <div class="form-group">
            <label for="down_payment">Down Payment</label>
            <input type="number" step="0.01" name="down_payment" id="down_payment" class="form-control" value="{{ $invoice->down_payment }}">
        </div>
        <div class="form-group">
            <label for="vat">VAT</label>
            <input type="number" step="0.01" name="vat" id="vat" class="form-control" value="{{ $invoice->vat }}">
        </div>
        <div class="form-group">
            <label for="grand_total">Grand Total</label>
            <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control" value="{{ $invoice->grand_total }}" required>
        </div>
        <div class="form-group">
            <label for="payment_schedule_type">Payment Schedule Type</label>
            <input type="text" name="payment_schedule_type" id="payment_schedule_type" class="form-control" value="{{ $invoice->payment_schedule_type }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
