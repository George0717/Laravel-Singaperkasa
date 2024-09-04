@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Riwayat Stok Barang</h1>

    <!-- Tampilkan tabel riwayat stok -->
    <table class="table">
        <thead>
            <tr>
                <th>Nomor SO</th>
                <th>Nama Barang</th>
                <th>Jumlah Berkurang</th>
                <th>Keterangan</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $history)
                <tr>
                    <td>{{ $history->sales_order->so_number }}</td>
                    <td>{{ $history->item_name }}</td>
                    <td>{{ $history->change_quantity }}</td>
                    <td>{{ $history->reason }}</td>
                    <td>{{ $history->date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
