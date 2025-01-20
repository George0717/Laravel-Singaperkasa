@extends('layouts.superAdmin')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Detail Log Aktivitas</h1>
    <div class="bg-white p-4 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold mb-2">Informasi Aktivitas</h2>
        <table class="table-auto w-full border border-gray-300 rounded-lg">
            <tbody>
                <tr class="border-b">
                    <th class="px-4 py-2 text-left">Aksi</th>
                    <td class="px-4 py-2">{{ $log->action }}</td>
                </tr>
                <tr class="border-b">
                    <th class="px-4 py-2 text-left">Pengguna</th>
                    <td class="px-4 py-2">{{ $log->user->name ?? 'Sistem' }}</td>
                </tr>
                <tr class="border-b">
                    <th class="px-4 py-2 text-left">Deskripsi</th>
                    <td class="px-4 py-2">{{ $log->description }}</td>
                </tr>
                <tr class="border-b">
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <td class="px-4 py-2">{{ $log->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left">Data Lama</th>
                    <td class="px-4 py-2">
                        <pre class="bg-gray-100 p-2 rounded">{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                    </td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left">Data Baru</th>
                    <td class="px-4 py-2">
                        <pre class="bg-gray-100 p-2 rounded">{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex gap-4">
        <form action="{{ route('superAdmin.salesOrders.restore', $log->id) }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                Restore Data
            </button>
        </form>
        <a href="{{ route('superAdmin.SalesOrders.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Kembali</a>
    </div>
</div>
@endsection
