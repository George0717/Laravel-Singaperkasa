@extends('layouts.superAdmin')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Log Aktivitas</h1>
    <div class="overflow-x-auto">
        <table class="table-auto w-full border border-gray-300 rounded-lg shadow-sm">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Aksi</th>
                    <th class="px-4 py-2">Pengguna</th>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $index => $log)
                <tr class="border-b border-gray-300 hover:bg-gray-100">
                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                    <td class="px-4 py-2">{{ $log->action }}</td>
                    <td class="px-4 py-2">{{ $log->user->name ?? 'Sistem' }}</td>
                    <td class="px-4 py-2">{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('superAdmin.riwayat.show', $log->id) }}" class="text-blue-500 hover:underline">Lihat Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links('pagination::tailwind') }}
    </div>
</div>
@endsection
