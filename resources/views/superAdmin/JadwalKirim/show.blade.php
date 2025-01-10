@extends('layouts.superAdmin')
@section('title', 'Detail Jadwal Kirim')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6 text-center">Detail Jadwal Kirim</h1>

    @if (session('success'))
        <div class="alert alert-success shadow-md mb-6">
            <strong>Berhasil!</strong> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg p-6">
        <section class="mb-8">
            <h2 class="text-xl font-semibold border-b pb-2 mb-4">Informasi Jadwal Kirim</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div><dt class="font-medium">Nomor SO:</dt> <dd>{{ $jadwalKirim->salesOrder->so_number }}</dd></div>
                <div><dt class="font-medium">Nama Pelanggan:</dt> <dd>{{ $jadwalKirim->salesOrder->customer_name }}</dd></div>
                <div><dt class="font-medium">Tanggal Pengiriman:</dt> <dd>{{ \Carbon\Carbon::parse($jadwalKirim->delivery_date)->format('j F Y') }}</dd></div>
                <div><dt class="font-medium">Keterangan:</dt> <dd>{{ $jadwalKirim->keterangan }}</dd></div>
            </dl>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold border-b pb-2 mb-4">Detail Sales Order</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div><dt class="font-medium">Nama Pelanggan:</dt> <dd>{{ $jadwalKirim->salesOrder->customer_name }}</dd></div>
                <div><dt class="font-medium">Alamat Pelanggan:</dt> <dd>{{ $jadwalKirim->salesOrder->customer_address ?? 'Tidak tersedia' }}</dd></div>
                <div><dt class="font-medium">Nomor PO:</dt> <dd>{{ $jadwalKirim->salesOrder->po_number ?? 'Tidak tersedia' }}</dd></div>
                <div><dt class="font-medium">Nomor SO:</dt> <dd>{{ $jadwalKirim->salesOrder->so_number }}</dd></div>
                <div><dt class="font-medium">Diskon:</dt> <dd>{{ number_format($jadwalKirim->salesOrder->discount, 0, ',', '.') }} {{ $jadwalKirim->salesOrder->discount_type }}</dd></div>
                <div><dt class="font-medium">DP:</dt> <dd>Rp {{ number_format($jadwalKirim->salesOrder->down_payment, 0, ',', '.') }}</dd></div>
                <div><dt class="font-medium">Total:</dt> <dd>Rp {{ number_format($jadwalKirim->salesOrder->grand_total, 0, ',', '.') }}</dd></div>
                <div><dt class="font-medium">PPN:</dt> <dd>{{ $jadwalKirim->salesOrder->vat > 0 ? $jadwalKirim->salesOrder->vat . '%' : 'Harga belum termasuk pajak' }}</dd></div>
            </dl>
        </section>

        <section>
            <h2 class="text-xl font-semibold border-b pb-2 mb-4">Detail Barang</h2>
            @if ($jadwalKirim->salesOrder->details && $jadwalKirim->salesOrder->details->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($jadwalKirim->salesOrder->details as $detail)
                        <div class="bg-gray-50 shadow rounded-lg p-4">
                            <h3 class="text-lg font-medium">{{ $detail->item_name ?? 'Tidak tersedia' }}</h3>
                            <ul class="mt-2 text-sm text-gray-600">
                                <li>Jumlah: {{ number_format($detail->quantity, 0, ',', '.') }}</li>
                                <li>Harga: Rp {{ number_format($detail->price, 0, ',', '.') }}</li>
                                <li>Total: Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</li>
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-red-500">Detail barang tidak tersedia.</p>
            @endif
        </section>

        <div class="mt-8 flex justify-between">
            <a href="{{ route('superAdmin.pdf.generate', ['jadwalKirim' => $jadwalKirim->id]) }}" class="btn btn-primary">
                Cetak PDF
            </a>
            <a href="{{ route('superAdmin.JadwalKirim.index') }}" class="text-blue-500 hover:text-blue-700">
                &lt; Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
