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
            <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200 mb-4">
                <h2 class="text-xl font-semibold mb-2">Detail Sales Order</h2>
                <p class="text-gray-700"><strong>Nama Pelanggan:</strong> {{ $jadwalKirim->salesOrder->customer_name }}</p>
                <p class="text-gray-700"><strong>Alamat Pelanggan:</strong> {{ $jadwalKirim->salesOrder->customer_address ?? 'Tidak tersedia' }}</p>
                <p class="text-gray-700"><strong>Nomor PO:</strong> {{ $jadwalKirim->salesOrder->po_number ?? 'Tidak tersedia' }}</p>
                <p class="text-gray-700"><strong>Nomor SO:</strong> {{ $jadwalKirim->salesOrder->so_number }}</p>
                <p class="text-gray-700"><strong>Diskon:</strong> {{ number_format($jadwalKirim->salesOrder->discount, 0, ',', '.') }} {{ $jadwalKirim->salesOrder->discount_type }}</p>
                <p class="text-gray-700"><strong>DP:</strong> Rp {{ number_format($jadwalKirim->salesOrder->down_payment, 0, ',', '.') }}</p>
                <p class="text-gray-700"><strong>Total:</strong> Rp {{ number_format($jadwalKirim->salesOrder->grand_total, 0, ',', '.') }}</p>
                @if ($jadwalKirim->salesOrder->vat > 0)
                    <p class="text-gray-700"><strong>PPN:</strong> {{ $jadwalKirim->salesOrder->vat }}%</p>
                @else
                    <p class="text-gray-700"><strong>PPN:</strong> Harga belum termasuk pajak</p>
                @endif
            </div>

            <!-- Detail Barang -->
            <div class="mt-6">
                @if ($jadwalKirim->salesOrder->details && $jadwalKirim->salesOrder->details->count() > 0)
                    <div class="flex flex-wrap gap-4">
                        @foreach ($jadwalKirim->salesOrder->details as $detail)
                            <div class="flex bg-white shadow-lg rounded-lg border border-gray-200 p-4 w-full sm:w-auto">
                                <div class="mr-4">
                                    <h2 class="text-lg font-semibold mb-2">{{ $detail->item_name ?? 'Tidak tersedia' }}</h2>
                                    <p class="text-gray-700">Jumlah: {{ number_format($detail->quantity, 0, ',', '.') }}</p>
                                    <p class="text-gray-700">Harga: Rp {{ number_format($detail->price, 0, ',', '.') }}</p>
                                    <p class="text-gray-700">Total: Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-red-500">Detail barang tidak tersedia.</p>
                @endif
            </div>
        </div>

        <!-- Button Print PDF -->
        <div class="mt-6">
            <a href="{{ route('pdf.generate', ['jadwalKirim' => $jadwalKirim->id]) }}" class="btn-primary">
                Cetak PDF
            </a>
        </div>

        <!-- Tombol Kembali -->
        <div class="mt-6">
            <a href="{{ route('JadwalKirim.index') }}" class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">
                &lt; Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
