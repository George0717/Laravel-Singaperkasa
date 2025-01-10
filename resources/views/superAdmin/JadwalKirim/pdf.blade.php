<!-- resources/views/pdf/jadwal_kirim.blade.php -->
@extends('layouts.pdf')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <!-- Bagian Header -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 28px; color: #4CAF50; font-weight: bold;">Jadwal Kirim</h1>
        <p style="font-size: 16px; color: #333; margin: 0;"><strong>Nomor SO:</strong> {{ $jadwalKirim->salesOrder->so_number }}</p>
    </div>

    <!-- Bagian Informasi Pelanggan -->
    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 20px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px; margin-bottom: 15px;">Informasi Pelanggan</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #ddd; width: 30%;"><strong>Nama Pelanggan:</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $jadwalKirim->salesOrder->customer_name }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Alamat:</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $jadwalKirim->salesOrder->customer_address }}</td>
            </tr>
            <!-- Tambahkan field lain jika diperlukan -->
        </table>
    </div>

    <!-- Bagian Detail Sales Order -->
    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 20px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px; margin-bottom: 15px;">Detail Sales Order</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 10px; border-bottom: 2px solid #ddd; text-align: left;">Item</th>
                    <th style="padding: 10px; border-bottom: 2px solid #ddd; text-align: left;">Jumlah</th>
                    <th style="padding: 10px; border-bottom: 2px solid #ddd; text-align: left;">Harga</th>
                    <th style="padding: 10px; border-bottom: 2px solid #ddd; text-align: left;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jadwalKirim->salesOrder->details as $detail)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: left;">{{ $detail->item_name }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: left;">{{ $detail->quantity }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: left;">Rp {{ number_format($detail->price, 2) }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: left;">Rp {{ number_format($detail->quantity * $detail->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bagian Tanggal Kirim dan Keterangan -->
    <div>
        <h2 style="font-size: 20px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px; margin-bottom: 15px;">Informasi Tambahan</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; width: 30%;"><strong>Tanggal Kirim:</strong></td>
                <td style="padding: 10px;">{{ \Carbon\Carbon::parse($jadwalKirim->delivery_date)->format('j F Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; width: 30%;"><strong>Keterangan:</strong></td>
                <td style="padding: 10px;">{{ $jadwalKirim->keterangan ?? 'Tidak ada keterangan' }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
