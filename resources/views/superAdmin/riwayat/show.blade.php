@extends('layouts.superAdmin')

@section('content')
<div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-semibold mb-4">Detail Riwayat</h1>
    <div class="mb-4">
        <p><strong>Aksi:</strong> <span class="capitalize">{{ $action === 'deleted' ? 'Hapus' : ucfirst($action)
                }}</span></p>
        <p><strong>Tanggal:</strong> {{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}
        </p>
        <p><strong>Deskripsi:</strong> {{ $log->description ?? 'Deskripsi tidak tersedia' }}</p>
    </div>

    {{-- Switch untuk menangani aksi --}}
    @switch($action)
    {{-- Tampilkan Data untuk Aksi Created --}}
    @case('created')
    @if($model)
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Data Baru yang Dibuat</h3>
        <table class="min-w-full table-auto bg-gray-100 rounded-lg">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left bg-gray-200 font-medium text-sm text-gray-600">Kolom</th>
                    <th class="px-4 py-2 text-left bg-gray-200 font-medium text-sm text-gray-600">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @if ($log->model_type === 'App\Models\SalesOrder')
                {{-- Tabel untuk data utama SalesOrder --}}
                <table class="w-full border-collapse border border-gray-300 text-left">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 font-bold text-gray-700 bg-gray-100">Atribut</th>
                            <th class="border border-gray-300 px-4 py-2 font-bold text-gray-700 bg-gray-100">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Baris untuk data utama SalesOrder --}}
                        @foreach(Arr::only($model->toArray(), [
                            'customer_name', 'nama_sales', 'so_number', 'discount', 'discount_type','vat',
                            'payment_type', 'grand_total', 'deleted_at']) as $key => $value)
                            <tr class="border-t">
                                <td class="border border-gray-300 px-4 py-2 font-medium text-gray-700">
                                    @switch($key)
                                    @case('customer_name') Nama Customer @break
                                    @case('nama_sales') Nama Sales @break
                                    @case('so_number') Nomor SO @break
                                    @case('discount') Diskon @break
                                    @case('discount_type') Tipe Diskon @break
                                    @case('vat') PPN @break
                                    @case('payment_type') Tipe Pembayaran @break
                                    @case('grand_total') Total Grand @break
                                    @case('deleted_at') Tanggal Dihapus @break
                                    @default {{ ucwords(str_replace('_', ' ', $key)) }}
                                    @endswitch
                                </td>
                                <td class="border border-gray-300 px-4 py-2 text-gray-700">
                                    {{ is_null($value) ? 'Tidak Tersedia' :
                                    ($key === 'deleted_at'
                                    ? \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')
                                    : ($key === 'discount' || $key === 'vat' || $key === 'grand_total'
                                    ? 'Rp. ' . number_format($value, 0, ',', '.')
                                    : $value))
                                    }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            
                {{-- Tabel untuk data detail dari SalesOrderDetail --}}
                <h2 class="mt-6 font-bold text-lg text-gray-700">Detail Item</h2>
                <table class="w-full border-collapse border border-gray-300 text-left mt-2">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 font-bold text-gray-700 bg-gray-100">No</th>
                            <th class="border border-gray-300 px-4 py-2 font-bold text-gray-700 bg-gray-100">Nama Item</th>
                            <th class="border border-gray-300 px-4 py-2 font-bold text-gray-700 bg-gray-100">Quantity</th>
                            <th class="border border-gray-300 px-4 py-2 font-bold text-gray-700 bg-gray-100">Harga</th>
                            <th class="border border-gray-300 px-4 py-2 font-bold text-gray-700 bg-gray-100">Total</th>
                            <th class="border border-gray-300 px-4 py-2 font-bold text-gray-700 bg-gray-100">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($model->details as $index => $detail)
                            <tr class="border-t">
                                <td class="border border-gray-300 px-4 py-2 text-gray-700">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-gray-700">{{ $detail->item_name }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-gray-700">{{ $detail->quantity }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-gray-700">Rp. {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-gray-700">Rp. {{ number_format($detail->total, 0, ',', '.') }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-gray-700">{{ $detail->remaining_quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="border border-gray-300 px-4 py-2 text-gray-700 text-center">Tidak ada data detail.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>            
                @elseif ($log->model_type === 'App\Models\JadwalKirim')
                @foreach(Arr::only($model->toArray(), ['delivery_date', 'keterangan', 'tujuan_pengiriman']) as $key =>
                $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        @switch($key)
                        @case('delivery_date') Tanggal Pengiriman @break
                        @case('keterangan') Keterangan @break
                        @case('tujuan_pengiriman') Tujuan Pengiriman @break
                        @default {{ ucwords(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' :
                        ($key === 'deleted_at'
                        ? \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')
                        : $value)
                        }}
                    </td>
                </tr>
                @endforeach
                @elseif ($log->model_type === 'App\Models\SuratJalan')
                @foreach(Arr::only($model->toArray(), ['plat_angkutan', 'tanggal_pengiriman', 'no_surat_jalan']) as $key
                => $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        @switch($key)
                        @case('plat_angkutan') Plat Angkutan @break
                        @case('tanggal_pengiriman') Tanggal Pengiriman @break
                        @case('no_surat_jalan') No Surat Jalan @break
                        @default {{ ucwords(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' :
                        ($key === 'deleted_at'
                        ? \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')
                        : $value)
                        }}
                    </td>
                </tr>
                @endforeach
                @elseif ($log->model_type === 'App\Models\Invoice')
                @foreach(Arr::only($model->toArray(), ['invoice_number', 'discount', 'down_payment', 'vat',
                'grand_total', 'payment_type']) as $key => $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        @switch($key)
                        @case('invoice_number') Nomor Faktur @break
                        @case('discount') Diskon @break
                        @case('down_payment') Uang Muka @break
                        @case('vat') PPN @break
                        @case('grand_total') Total Grand @break
                        @case('payment_type') Tipe Pembayaran @break
                        @default {{ ucwords(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' :
                        ($key === 'deleted_at'
                        ? \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')
                        : $value)
                        }}
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="2" class="px-4 py-2 text-gray-700 text-center">Model tidak ditemukan atau kolom tidak
                        relevan</td>
                </tr>

                @endif
            </tbody>
        </table>
    </div>
    @endif
    @break

    {{-- Tampilkan Data untuk Aksi Updated --}}
    @case('updated')
    @if($dataLama && $dataBaru)
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Data Lama</h3>
        <table class="min-w-full table-auto bg-gray-100 rounded-lg">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left bg-gray-200 font-medium text-sm text-gray-600">Kolom</th>
                    <th class="px-4 py-2 text-left bg-gray-200 font-medium text-sm text-gray-600">Nilai Lama</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataLama as $key => $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        {{ ucwords(str_replace('_', ' ', $key)) }}
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' : $value }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Data Baru</h3>
        <table class="min-w-full table-auto bg-gray-100 rounded-lg">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left bg-gray-200 font-medium text-sm text-gray-600">Kolom</th>
                    <th class="px-4 py-2 text-left bg-gray-200 font-medium text-sm text-gray-600">Nilai Baru</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataBaru as $key => $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        {{ ucwords(str_replace('_', ' ', $key)) }}
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' : $value }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-red-500">Data perubahan tidak tersedia. Pastikan log perubahan memiliki informasi data lama dan data
        baru.</p>
    @endif
    @break


    {{-- Tampilkan Data untuk Aksi Deleted --}}
    @case('deleted')
    @if($action === 'deleted' && $model)
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Data yang Dihapus</h3>
        <table class="min-w-full table-auto bg-gray-100 rounded-lg">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left bg-gray-200 font-medium text-sm text-gray-600">Kolom</th>
                    <th class="px-4 py-2 text-left bg-gray-200 font-medium text-sm text-gray-600">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @if ($log->model_type === 'App\Models\SalesOrder')
                {{-- Menampilkan data utama SalesOrder --}}
                @foreach(Arr::only($model->toArray(), [
                'customer_name', 'nama_sales', 'so_number', 'discount', 'discount_type','vat',
                'payment_type', 'grand_total', 'deleted_at']) as $key => $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        @switch($key)
                        @case('customer_name') Nama Customer @break
                        @case('nama_sales') Nama Sales @break
                        @case('so_number') Nomor SO @break
                        @case('discount') Diskon @break
                        @case('discount_type') Tipe Diskon @break
                        @case('vat') PPN @break
                        @case('payment_type') Tipe Pembayaran @break
                        @case('grand_total') Total Grand @break
                        @case('deleted_at') Tanggal Dihapus @break
                        @default {{ ucwords(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' :
                        ($key === 'deleted_at'
                        ? \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')
                        : ($key === 'discount' || $key === 'vat' || $key === 'grand_total'
                        ? 'Rp. ' . number_format($value, 0, ',', '.')
                        : $value))
                        }}
                    </td>
                </tr>
                @endforeach

                {{-- Menampilkan data detail dari SalesOrderDetail --}}
                @foreach($model->details as $detail)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        Detail Item
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        <strong>Nama Item:</strong> {{ $detail->item_name }} <br>
                        <strong>Quantity:</strong> {{ $detail->quantity }} <br>
                        <strong>Harga:</strong> Rp. {{ number_format($detail->price, 0, ',', '.') }} <br>
                        <strong>Total:</strong> Rp. {{ number_format($detail->total, 0, ',', '.') }} <br>
                        <strong>Sisa:</strong> {{ $detail->remaining_quantity }}
                    </td>
                </tr>
                @endforeach

                @elseif ($log->model_type === 'App\Models\JadwalKirim')
                @foreach(Arr::only($model->toArray(), ['delivery_date', 'keterangan', 'tujuan_pengiriman']) as $key =>
                $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        @switch($key)
                        @case('delivery_date') Tanggal Pengiriman @break
                        @case('keterangan') Keterangan @break
                        @case('tujuan_pengiriman') Tujuan Pengiriman @break
                        @default {{ ucwords(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' :
                        ($key === 'deleted_at'
                        ? \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')
                        : $value)
                        }}
                    </td>
                </tr>
                @endforeach
                @elseif ($log->model_type === 'App\Models\SuratJalan')
                @foreach(Arr::only($model->toArray(), ['plat_angkutan', 'tanggal_pengiriman', 'no_surat_jalan']) as $key
                => $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        @switch($key)
                        @case('plat_angkutan') Plat Angkutan @break
                        @case('tanggal_pengiriman') Tanggal Pengiriman @break
                        @case('no_surat_jalan') No Surat Jalan @break
                        @default {{ ucwords(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' :
                        ($key === 'deleted_at'
                        ? \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')
                        : $value)
                        }}
                    </td>
                </tr>
                @endforeach
                @elseif ($log->model_type === 'App\Models\Invoice')
                @foreach(Arr::only($model->toArray(), ['invoice_number', 'discount', 'down_payment', 'vat',
                'grand_total', 'payment_type']) as $key => $value)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium text-gray-700">
                        @switch($key)
                        @case('invoice_number') Nomor Faktur @break
                        @case('discount') Diskon @break
                        @case('down_payment') Uang Muka @break
                        @case('vat') PPN @break
                        @case('grand_total') Total Grand @break
                        @case('payment_type') Tipe Pembayaran @break
                        @default {{ ucwords(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ is_null($value) ? 'Tidak Tersedia' :
                        ($key === 'deleted_at'
                        ? \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')
                        : $value)
                        }}
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="2" class="px-4 py-2 text-gray-700 text-center">Model tidak ditemukan atau kolom tidak
                        relevan</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <p><strong>Dihapus Oleh:</strong> {{ $log->user ? $log->user->name : 'Tidak diketahui' }}</p>
    </div>

    <form action="{{ route('activityLog.restore', $log->id) }}" method="POST" class="mt-4">
        @csrf
        @method('PUT')

        @php
        // Ambil model terkait dari log
        $modelClass = $log->model_type;
        $model = $modelClass::withTrashed()->find($log->model_id);
        @endphp

        {{-- Periksa apakah model sudah dipulihkan sebelumnya --}}
        @if ($model && $model->trashed() && !$model->restored_at)
        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
            Kembalikan Data
        </button>
        @else
        <button type="button" class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed" disabled>
            Data Sudah Dipulihkan
        </button>
        @endif
    </form>

    @endif
    @break

    {{-- Default Jika Tidak Ada Data --}}
    @default
    <p class="text-red-500">Aksi tidak dikenali atau data tidak tersedia.</p>
    @endswitch

    <a href="{{ route('admin.riwayat.index') }}" class="block mt-6 text-blue-500 hover:underline">
        Kembali ke Daftar Riwayat
    </a>
</div>
@endsection