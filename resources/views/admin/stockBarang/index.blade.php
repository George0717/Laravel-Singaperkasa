@extends('layouts.superAdmin')
@section('title', 'Stock Barang')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Stock Barang</h1>

    <a href="{{ route('superAdmin.stockBarang.create') }}" 
       class="inline-block bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-300">
        Tambah Barang
    </a>

    <div class="overflow-x-auto mt-6">
        <table class="table-auto w-full border-collapse border border-gray-300 shadow-lg rounded-lg">
            <thead class="bg-gradient-to-r from-gray-200 to-gray-300">
                <tr>
                    <th class="px-6 py-4 text-left text-gray-700 font-semibold">Nama Barang</th>
                    <th class="px-6 py-4 text-left text-gray-700 font-semibold">Tipe Barang</th>
                    <th class="px-6 py-4 text-left text-gray-700 font-semibold">Jumlah</th>
                    <th class="px-6 py-4 text-left text-gray-700 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($stockBarang as $item)
                <tr class="hover:bg-gray-100 transition duration-300">
                    <td class="px-6 py-4 text-gray-800">{{ $item->nama_barang }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $item->tipe_barang }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $item->jumlah_barang }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('superAdmin.stockBarang.edit', $item->id) }}" 
                           class="text-blue-600 hover:text-blue-800 font-medium underline transition duration-300">
                            Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
