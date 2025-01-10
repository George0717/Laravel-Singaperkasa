@extends('layouts.app')

@section('content')
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
                <td>{{ $history->salesOrder->so_number }}</td>
                <td>{{ $history->item_name }}</td>
                <td>{{ $history->change_quantity }}</td>
                <td>{{ $history->reason }}</td>
                <td>{{ $history->date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection
