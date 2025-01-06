@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Riwayat Aktivitas</h1>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($activities as $activity)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->created_at->format('d-m-Y H:i:s') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->description }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->log_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->subject_id }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection