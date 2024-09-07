@extends('layouts.app')

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

<div class="container mx-auto px-4">
    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="{{ route('suratJalan.index') }}" class="btn btn-secondary">Kembali ke Daftar Surat Jalan</a>
    </div>

    <!-- Tombol Generate PDF -->
    <div class="mb-4">
        <a href="{{ route('suratJalan.generate', $suratJalan->id) }}" class="btn btn-primary">Unduh PDF</a>
    </div>

    <!-- Animasi Loading -->
    <div id="loading-surat-jalan" class="skeleton p-4 rounded-lg mb-4">
        <h2 class="text-2xl font-semibold skeleton mb-2">Memuat Surat Jalan...</h2>
        <div class="grid grid-cols-2 gap-4">
            <div class="skeleton h-6 w-full"></div>
            <div class="skeleton h-6 w-full"></div>
        </div>
    </div>

    <!-- Konten Surat Jalan -->
    <div id="surat-jalan-content" class="hidden">
        <h2 class="text-2xl font-semibold mb-4">Detail Surat Jalan</h2>

        <div class="mb-4">
            <p><strong>Nama Customer:</strong> {{ $suratJalan->salesOrder->customer_name }}</p>
            <p><strong>Nomor Surat Jalan:</strong> {{ $suratJalan->no_surat_jalan }}</p>
            <p><strong>Tanggal Pengiriman:</strong> {{ \Carbon\Carbon::parse($suratJalan->tanggal_pengiriman)->translatedFormat('d F Y') }}</p>
        </div>

        <!-- Tabel Barang -->
        <h3 class="text-xl font-semibold mb-2">Daftar Barang</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($suratJalan->suratJalanDetails as $detail)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $detail->salesOrderDetail->item_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-500">Tidak ada barang dalam surat jalan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script untuk menghilangkan loading skeleton dan menampilkan konten -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('loading-surat-jalan').classList.add('hidden');
        document.getElementById('surat-jalan-content').classList.remove('hidden');
    });
</script>
@endsection
