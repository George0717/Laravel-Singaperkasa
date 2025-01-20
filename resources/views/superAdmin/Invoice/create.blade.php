@extends('layouts.superAdmin')

@section('title', 'Create Invoice')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-8 text-center text-primary">Create Invoice</h1>

    <!-- Pesan Sukses -->
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Form untuk membuat invoice -->
    <form action="{{ route('superAdmin.invoice.store') }}" method="POST" class="bg-white p-8 shadow-lg rounded-lg space-y-6">
        @csrf

        <!-- Nomor Invoice -->
        <div class="mb-4">
            <label for="invoice_number" class="block text-sm font-medium text-gray-700">Invoice Number</label>
            <input type="text" id="invoice_number" name="invoice_number" value="{{ $invoiceNumber }}" readonly
                class="form-input mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md p-2">
            @error('invoice_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pemilihan Sales Order -->
        <div class="mb-4">
            <label for="sales_order_id" class="block text-sm font-medium text-gray-700">Sales Order</label>
            <select id="sales_order_id" name="sales_order_id" class="form-select mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                <option value="" disabled selected>Select Sales Order</option>
                @foreach ($salesOrders as $order)
                <option value="{{ $order->id }}">
                    {{ $order->so_number }}
                </option>
                @endforeach
            </select>
            @error('sales_order_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Simpan -->
        <div class="text-center mt-6">
            <button type="submit" class="bg-primary text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition ease-in-out duration-300">
                Save Invoice
            </button>
        </div>
    </form>
</div>
@endsection
