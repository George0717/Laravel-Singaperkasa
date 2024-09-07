@extends('layouts.app')
@section('title', 'Edit Surat Jalan')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Edit Surat Jalan</h1>

    <!-- Pesan Sukses -->
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <form action="{{ route('suratJalan.update', $suratJalan->id) }}" method="post"
        class="bg-white p-8 shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Pemilihan Sales Order -->
        <div class="mb-4">
            <label for="sales_order_id" class="block text-sm font-medium text-gray-700">Pilih Sales Order</label>
            <select id="sales_order_id" name="sales_order_id" class="form-select mt-1 block w-full" required>
                <option value="" disabled>Pilih Sales Order</option>
                @foreach ($salesOrders as $salesOrder)
                <option value="{{ $salesOrder->id }}" {{ $suratJalan->sales_order_id == $salesOrder->id ? 'selected' :
                    '' }}>
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
            <label for="tanggal_pengiriman" class="block text-sm font-medium text-gray-700">Tanggal Pengiriman</label>
            <input type="date" id="tanggal_pengiriman" name="tanggal_pengiriman" class="form-input mt-1 block w-full"
                value="{{ $suratJalan->tanggal_pengiriman }}" required>
            @error('tanggal_pengiriman')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Plat Angkutan -->
        <div class="mb-4">
            <label for="plat_angkutan" class="block text-sm font-medium text-gray-700">Plat Angkutan</label>
            <input type="text" id="plat_angkutan" name="plat_angkutan" class="form-input mt-1 block w-full"
                value="{{ $suratJalan->plat_angkutan }}">
            @error('plat_angkutan')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nomor Surat Jalan -->
        <div class="form-group">
            <label for="nomor_surat_jalan">Nomor Surat Jalan</label>
            <input type="text" class="form-control" id="nomor_surat_jalan" name="nomor_surat_jalan"
                value="{{ old('nomor_surat_jalan', $suratJalan->no_surat_jalan) }}" required>
            @error('nomor_surat_jalan')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Detail Surat Jalan -->
        <div id="surat-jalan-details" class="overflow-x-auto mt-6">
            <div id="details-container" class="flex flex-wrap gap-4">
                @if (!empty($suratJalan->suratJalanDetails) && $suratJalan->suratJalanDetails->isNotEmpty())
                @foreach ($suratJalan->suratJalanDetails as $detail)
                <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mb-4">
                    <h2 class="text-lg font-semibold mb-2">{{ $detail->salesOrderDetail->item_name }}</h2>
                    <p class="text-gray-700">Jumlah: <input type="number"
                            name="items[{{ $detail->salesOrderDetail->id }}][quantity]" value="{{ $detail->quantity }}"
                            min="1" class="form-input w-full" readonly /></p>
                    <input type="hidden" name="items[{{ $detail->salesOrderDetail->id }}][id]"
                        value="{{ $detail->salesOrderDetail->id }}" />
                    <p class="text-gray-700">Harga: Rp {{ number_format($detail->salesOrderDetail->price, 0, ',', '.')
                        }}</p>
                    <p class="text-gray-700">Total: Rp {{ number_format($detail->quantity *
                        $detail->salesOrderDetail->price, 0, ',', '.') }}</p>
                </div>
                @endforeach
                @else
                <p class="text-red-500">Tidak ada detail yang tersedia.</p>
                @endif
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="mb-4">
            <button type="submit" class="btn btn-primary hover:bg-blue-600 transition duration-300 ease-in-out">
                <span class="relative">Simpan</span>
            </button>
        </div>

        <a href="{{ route('suratJalan.index') }}"
            class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">
            &lt; Kembali ke Daftar
        </a>
    </form>
</div>

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
                        if (data.sales_order_details && Array.isArray(data.sales_order_details)) {
                            var suratJalanHtml = `
                                <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mb-4">
                                    <h2 class="text-xl font-semibold mb-2">Detail Surat Jalan</h2>
                                    <p class="text-gray-700"><strong>Nama Pelanggan:</strong> ${data.sales_order.customer_name}</p>
                                    <p class="text-gray-700"><strong>Alamat Pelanggan:</strong> ${data.sales_order.customer_address}</p>
                                    <p class="text-gray-700"><strong>Dikirim Oleh:</strong> PT. Singa Perkasa Abadi</p>
                                </div>
                            `;

                            var detailsHtml = '';
                            data.sales_order_details.forEach(function(detail) {
                                detailsHtml += `
                                    <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mb-4">
                                        <h2 class="text-lg font-semibold mb-2">${detail.item_name}</h2>
                                        <p class="text-gray-700">Jumlah: <input type="number" name="items[${detail.id}][quantity]" value="1" min="1" max="${detail.quantity}" class="form-input w-full" /></p>
                                        <input type="hidden" name="items[${detail.id}][id]" value="${detail.id}" />
                                        <p class="text-gray-700">Harga: Rp ${parseFloat(detail.price).toLocaleString()}</p>
                                        <p class="text-gray-700">Total: Rp ${parseFloat(detail.quantity * detail.price).toLocaleString()}</p>
                                    </div>
                                `;
                            });

                            $('#details-container').html(suratJalanHtml + detailsHtml);
                        } else {
                            $('#details-container').html('<p class="text-red-500">Tidak ada detail yang tersedia.</p>');
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
    });
</script>

@endsection