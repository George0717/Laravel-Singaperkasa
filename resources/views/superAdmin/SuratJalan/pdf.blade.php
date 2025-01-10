@extends('layouts.pdf')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; color: #4CAF50; font-weight: bold;">Surat Jalan</h1>
        <p style="font-size: 18px; color: #333;"><strong>Nomor Surat Jalan:</strong> {{ $suratJalan->no_surat_jalan }}</p>
    </div>
    <!-- Customer Info Section -->
    <div style="margin-bottom: 30px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Nama Customer:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $suratJalan->salesOrder->customer_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Tanggal Pengiriman:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ \Carbon\Carbon::parse($suratJalan->tanggal_pengiriman)->format('d F Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Order Details Section -->
    <div style="margin-top: 40px;">
        <h3 style="text-align: center; color: #4CAF50; margin-bottom: 20px;">Daftar Barang</h3>
        @if($suratJalan->suratJalanDetails && $suratJalan->suratJalanDetails->count() > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Nama Barang</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suratJalan->suratJalanDetails as $detail)
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $detail->salesOrderDetail->item_name }}</td>
                            <td style="padding: 10px; text-align: center; border-bottom: 1px solid #ddd;">{{ $detail->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #FF0000;">Tidak ada barang dalam surat jalan ini.</p>
        @endif
    </div>

    <!-- Footer Section -->
    <div style="margin-top: 40px; display: flex; justify-content: space-between;">
        <!-- Signature & Stamp of Store -->
        <div style="text-align: center; width: 48%;">
            <p style="font-size: 14px; font-weight: bold;">Tanda Tangan & Cap Toko:</p>
            <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 20px;"></div>
        </div>
        <!-- Signature & Name of Staff -->
        <div style="text-align: center; width: 48%;">
            <p style="font-size: 14px; font-weight: bold;">Tanda Tangan & Nama Staff:</p>
            <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 20px;"></div>
        </div>
    </div>
    <p style="font-size: 12px; color: #888; text-align: center; margin-top: 20px;">Terima kasih atas kerjasama Anda!</p>
</div>
@endsection
