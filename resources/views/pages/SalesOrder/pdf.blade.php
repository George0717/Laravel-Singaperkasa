@extends('layouts.pdf')

@section('content')
<div class="container" style="font-family: Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.5;">
    <!-- Header Section -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 20px; color: #4CAF50; font-weight: bold; margin-bottom: 5px;">Pesanan Penjualan</h1>
        <p style="font-size: 14px; color: #333; margin: 0;"><strong>Nomor Pesanan:</strong> {{ $salesOrder->so_number }}</p>
    </div>

    <!-- Customer Info Section -->
    <div style="margin-bottom: 25px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; width: 25%;"><strong>Nama Pelanggan:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $salesOrder->customer_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; width: 25%;"><strong>Alamat Pelanggan:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $salesOrder->customer_address }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; width: 25%;"><strong>Nomor PO:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $salesOrder->po_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; width: 25%;"><strong>Tanggal PO:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $salesOrder->po_date->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Order Details Section -->
    <div style="margin-top: 20px;">
        <h3 style="text-align: left; color: #4CAF50; margin-bottom: 10px;">Detail Pesanan</h3>
        @if($salesOrder->details && $salesOrder->details->count() > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2; text-align: left;">
                        <th style="padding: 8px; border-bottom: 1px solid #ddd; width: 50%;">Nama Barang</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd; text-align: center; width: 15%;">Jumlah</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right; width: 20%;">Harga</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right; width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesOrder->details as $detail)
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $detail->item_name }}</td>
                            <td style="padding: 8px; text-align: center; border-bottom: 1px solid #ddd;">{{ $detail->quantity }}</td>
                            <td style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;">Rp. {{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;">Rp. {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #FF0000; font-size: 14px;">Detail pesanan tidak ditemukan.</p>
        @endif
    </div>

    <!-- Footer Section -->
    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #888;">
        <p>Terima kasih atas kerja sama Anda!</p>
    </div>
</div>
@endsection
