@extends('layouts.superAdmin')
@section('title', 'Stock Barang')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Stock Barang</h1>
    <a href="{{ route('superAdmin.stockBarang.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
        Tambah Barang
    </a>
    <table class="table-auto w-full mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="px-4 py-2">Nama Barang</th>
                <th class="px-4 py-2">Tipe Barang</th>
                <th class="px-4 py-2">Jumlah</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockBarang as $item)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $item->nama_barang }}</td>
                <td class="px-4 py-2">{{ $item->tipe_barang }}</td>
                <td class="px-4 py-2">{{ $item->jumlah_barang }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('superAdmin.stockBarang.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                    <form action="{{ route('superAdmin.stockBarang.destroy', $item->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
