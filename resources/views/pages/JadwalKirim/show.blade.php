@extends('layouts.app')
@section('title', 'Detail Jadwal Kirim')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Detail Jadwal Kirim</h1>

    <!-- Pesan Sukses -->
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white p-8 shadow-md rounded-lg">
        <!-- Detail Jadwal Kirim -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Informasi Jadwal Kirim</h2>
            <p class="text-gray-700"><strong>Nomor SO:</strong> {{ $jadwalKirim->salesOrder->so_number }}</p>
            <p class="text-gray-700"><strong>Nama Pelanggan:</strong> {{ $jadwalKirim->salesOrder->customer_name }}</p>
            <p class="text-gray-700"><strong>Tanggal Pengiriman:</strong> {{ \Carbon\Carbon::parse($jadwalKirim->delivery_date)->format('j F Y') }}</p>
            <p class="text-gray-700"><strong>Keterangan:</strong> {{ $jadwalKirim->keterangan }}</p>
        </div>

        <!-- Detail Sales Order -->
        <div id="sales-order-details" class="overflow-x-auto mt-6">
            <div id="details-container" class="flex flex-wrap gap-4">
                <!-- Kartu akan dimasukkan di sini secara dinamis -->
            </div>
        </div>

        <!-- Button Print PDF -->
        <div class="mt-6">
            <button id="print-pdf" class="btn-primary">
                Cetak PDF
            </button>
        </div>

        <!-- Tombol Kembali -->
        <div class="mt-6">
            <a href="{{ route('JadwalKirim.index') }}"
                class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">
                &lt; Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Pre-select sales order and trigger change to load details
        var salesOrderId = '{{ $jadwalKirim->sales_order_id }}';
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
        }

        // Handle PDF button click
        $('#print-pdf').click(function() {
    window.location.href = "{{ route('pdf.generate', ['jadwalKirim' => $jadwalKirim->id]) }}";
});
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