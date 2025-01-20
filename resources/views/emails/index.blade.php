@extends('layouts.superAdmin')

@section('title', 'Pengingat Bayaran')
@section('content')
<h1>Dear {{ $order->customer_name }}</h1>
<p>This is a reminder that your payment for Sales Order <strong>{{ $order->so_number }}</strong> is due today.</p>
<p><strong>Due Date:</strong> {{ $order->due_date }}</p>
<p>Please ensure the payment is completed to avoid any interruptions.</p>
<p>Thank you.</p>

@endsection