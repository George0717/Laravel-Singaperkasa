@extends('layouts.admin')

@section('content')

<!-- Skeleton Loading CSS -->
<style>
    .skeleton {
        background-color: #e2e8f0;
        background-image: linear-gradient(90deg, #e2e8f0 25%, #f8fafc 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
    }

    @keyframes skeleton-loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
</style>

<div class="container mx-auto px-6 py-8">
    <!-- Header Buttons -->
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.suratJalan.index') }}" class="btn btn-secondary bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md shadow">
            Kembali
        </a>
        <a href="{{ route('admin.suratJalan.generate', $suratJalan->id) }}" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
            Unduh PDF
        </a>
    </div>

    <!-- Loading Skeleton -->
    <div id="loading-surat-jalan" class="skeleton p-6 rounded-lg mb-6 shadow-lg">
        <h2 class="text-2xl font-semibold skeleton mb-4">Memuat Surat Jalan...</h2>
        <div class="skeleton h-4 w-3/4 mb-2"></div>
        <div class="skeleton h-4 w-1/2"></div>
    </div>

    <!-- Surat Jalan Content -->
    <div id="surat-jalan-content" class="hidden">
        <!-- Detail Surat Jalan -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">Detail Surat Jalan</h2>
            <div class="space-y-4 text-gray-600">
                <p><strong>Nama Customer:</strong> {{ $suratJalan->salesOrder->customer_name }}</p>
                <p><strong>Alamat Pelanggan:</strong> {{ $suratJalan->salesOrder->customer_address }}</p>
                <p><strong>Nomor Surat Jalan:</strong> {{ $suratJalan->no_surat_jalan }}</p>
                <p><strong>Tanggal Pengiriman:</strong> {{ \Carbon\Carbon::parse($suratJalan->tanggal_pengiriman)->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <!-- Daftar Barang -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-700">Daftar Barang</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border rounded-lg">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Nama Barang</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($suratJalan->suratJalanDetails as $detail)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $detail->salesOrderDetail->item_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $detail->quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-gray-500">Tidak ada barang dalam surat jalan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script for toggling loading and content -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('loading-surat-jalan').classList.add('hidden');
        document.getElementById('surat-jalan-content').classList.remove('hidden');
    });
</script>

@endsection
