@extends('layouts.app')
@section('title', 'Update Jadwal Kirim')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Edit Jadwal Kirim</h1>

    <!-- Pesan Sukses -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('jadwalKirim.update', $jadwalKirim->id) }}" method="POST" class="bg-white p-8 shadow-md rounded-lg">
        @csrf
        @method('PUT')

        <!-- Pemilihan Sales Order -->
        <div class="mb-4">
            <label for="sales_order_id" class="block text-sm font-medium text-gray-700">Pilih Sales Order</label>
            <select id="sales_order_id" name="sales_order_id" class="form-select mt-1 block w-full" required>
                <option value="" disabled>Pilih Sales Order</option>
                @foreach ($salesOrders as $salesOrder)
                    <option value="{{ $salesOrder->id }}" {{ $salesOrder->id == $jadwalKirim->sales_order_id ? 'selected' : '' }}>
                        {{ $salesOrder->so_number }} - {{ $salesOrder->customer_name }}
                    </option>
                @endforeach
            </select>
            @error('sales_order_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal Pengiriman -->
        <div class="mb-4">
            <label for="delivery_date" class="block text-sm font-medium text-gray-700">Tanggal Pengiriman</label>
            <input type="date" id="delivery_date" name="delivery_date" value="{{ $jadwalKirim->delivery_date }}" class="form-input mt-1 block w-full" required>
            @error('delivery_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Keterangan -->
        <div class="mb-4">
            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea id="keterangan" name="keterangan" rows="4" class="form-textarea mt-1 block w-full">{{ $jadwalKirim->keterangan }}</textarea>
            @error('keterangan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Detail Sales Order -->
        <div id="sales-order-details" class="overflow-x-auto mt-6">
            <div id="details-container" class="flex flex-wrap gap-4">
                <!-- Kartu akan dimasukkan di sini secara dinamis -->
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="mb-4">
            <button type="submit" class="btn btn-primary hover:bg-blue-600 transition duration-300 ease-in-out">
                <span class="relative">Simpan</span>
            </button>
        </div>

        <a href="{{ route('JadwalKirim.index') }}" class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">
            &lt; Kembali ke Daftar
        </a>
    </form>
</div>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sales_order_id').on('change', function() {
            var salesOrderId = $(this).val();
            if (salesOrderId) {
                $.ajax({
                    url: '{{ route('salesOrder.details') }}',
                    type: 'GET',
                    data: { sales_order_id: salesOrderId },
                    success: function(data) {
                        if (data.sales_order) {
                            var vat = parseFloat(data.sales_order.vat);
                            var vatHtml = vat > 0
                                ? `<p class="text-gray-700"><strong>PPN:</strong> ${vat.toLocaleString()} %</p>`
                                : '<p class="text-gray-700"><strong>PPN:</strong> Harga belum termasuk pajak</p>';

                            var salesOrderHtml = `
                                <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mb-4 transform transition-transform duration-500 animate-slide-in-left">
                                    <h2 class="text-xl font-semibold mb-2">Detail Sales Order</h2>
                                    <p class="text-gray-700"><strong>Nama Pelanggan:</strong> ${data.sales_order.customer_name}</p>
                                    <p class="text-gray-700"><strong>Alamat Pelanggan:</strong> ${data.sales_order.customer_address}</p>
                                    <p class="text-gray-700"><strong>Nomor PO:</strong> ${data.sales_order.po_number}</p>
                                    <p class="text-gray-700"><strong>Nomor SO:</strong> ${data.sales_order.so_number}</p>
                                    <p class="text-gray-700"><strong>Diskon:</strong> ${parseFloat(data.sales_order.discount).toLocaleString()} ${data.sales_order.discount_type}</p>
                                    <p class="text-gray-700"><strong>DP:</strong> Rp ${parseFloat(data.sales_order.down_payment).toLocaleString()}</p>
                                    <p class="text-gray-700"><strong>Total:</strong> Rp ${parseFloat(data.sales_order.grand_total).toLocaleString()}</p>
                                    ${vatHtml}
                                </div>
                            `;

                            var detailsHtml = '';
                            data.sales_order_details.forEach(function(detail) {
                                detailsHtml += `
                                    <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mb-4 transform transition-transform duration-500 animate-slide-in-left">
                                        <h2 class="text-lg font-semibold mb-2">${detail.item_name}</h2>
                                        <p class="text-gray-700">Jumlah: ${detail.quantity}</p>
                                        <p class="text-gray-700">Harga: Rp ${parseFloat(detail.price).toLocaleString()}</p>
                                        <p class="text-gray-700">Total: Rp ${parseFloat(detail.quantity * detail.price).toLocaleString()}</p>
                                    </div>
                                `;
                            });

                            $('#details-container').html(salesOrderHtml + detailsHtml);
                        } else {
                            $('#details-container').html('<p class="text-red-500">Sales Order tidak ditemukan.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Kesalahan AJAX:', status, error);
                        $('#details-container').html('<p class="text-red-500">Kesalahan saat mengambil detail.</p>');
                    }
                });
            } else {
                $('#details-container').empty();
            }
        });

        // Pre-select sales order and trigger change to load details
        var selectedSalesOrderId = $('#sales_order_id').val();
        if (selectedSalesOrderId) {
            $('#sales_order_id').trigger('change');
        }
    });
</script>

<style>
    @keyframes slide-in-left {
        0% {
            transform: translateX(-100%);
            opacity: 0;
        }
        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animate-slide-in-left {
        animation: slide-in-left 0.5s ease-out;
    }
</style>
@endsection
