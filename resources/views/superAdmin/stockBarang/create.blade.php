@extends('layouts.superAdmin')
@section('title', 'Tambah Stock Barang')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Tambah Stock Barang</h1>
    <form action="{{ route('superAdmin.stockBarang.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="tipe_barang" class="block text-sm font-medium text-gray-700">Ukuran Barang</label>
            <input type="text" id="tipe_barang" name="tipe_barang" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="jumlah_barang" class="block text-sm font-medium text-gray-700">Jumlah Barang</label>
            <input type="number" id="jumlah_barang" name="jumlah_barang" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">Simpan</button>
    </form>
</div>
@endsection
