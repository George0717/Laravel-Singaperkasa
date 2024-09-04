@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Stock Barang</h1>

    <!-- Tampilkan tabel stok barang -->
    <table class="table">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah Stok</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        <a href="{{ route('stock.show', $item->id) }}" class="btn btn-info">Lihat Riwayat</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
