@extends('layouts.pdf')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 28px; color: #4CAF50; font-weight: bold;">Detail Invoice</h1>
        <p style="font-size: 16px; color: #333; margin: 0;"><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</p>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 20px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px;">Sales Order</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; width: 30%;"><strong>Pelanggan:</strong></td>
                <td style="padding: 10px;">{{ $invoice->salesOrder->customer_name }}</td>
            </tr>
            <tr>
                <td style="padding: 10px;"><strong>Alamat:</strong></td>
                <td style="padding: 10px;">{{ $invoice->salesOrder->customer_address }}</td>
            </tr>
            <tr>
                <td style="padding: 10px;"><strong>Tipe Pembayaran:</strong></td>
                <td style="padding: 10px;">{{ $invoice->salesOrder->payment_type }}</td>
            </tr>
            <tr>
                <td style="padding: 10px;"><strong>Nomor PO:</strong></td>
                <td style="padding: 10px;">{{ $invoice->salesOrder->po_number }}</td>
            </tr>
            <tr>
                <td style="padding: 10px;"><strong>Tanggal PO:</strong></td>
                <td style="padding: 10px;">{{ \Carbon\Carbon::parse($invoice->salesOrder->po_date)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 20px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px;">Detail Invoice</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 10px; border-bottom: 2px solid #ddd; text-align: left;">Keterangan</th>
                    <th style="padding: 10px; border-bottom: 2px solid #ddd; text-align: left;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px;">Diskon</td>
                    <td style="padding: 10px;">{{ $invoice->discount == 0 ? 'Harga Normal' : 'Rp ' . number_format($invoice->discount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Uang Muka</td>
                    <td style="padding: 10px;">Rp {{ number_format($invoice->down_payment, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">PPN</td>
                    <td style="padding: 10px;">{{ $invoice->vat == 0 ? 'Harga Sebelum Pajak' : number_format($invoice->vat, 2, ',', '.') . '%' }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">Total Keseluruhan</td>
                    <td style="padding: 10px; font-weight: bold; color: #4CAF50;">Rp {{ number_format($invoice->grand_total, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div>
        <h2 style="font-size: 20px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px;">Surat Jalan</h2>
        @foreach($invoice->salesOrder->suratJalans as $suratJalan)
        <div style="margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
            <p><strong>No. Surat Jalan:</strong> {{ $suratJalan->no_surat_jalan }}</p>
            <p><strong>Tanggal Pengiriman:</strong> {{ \Carbon\Carbon::parse($suratJalan->tanggal_pengiriman)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
            <p><strong>Plat Angkutan:</strong> {{ $suratJalan->plat_angkutan }}</p>
            <p><strong>Jumlah Barang:</strong> {{ $suratJalan->suratJalanDetails->sum('quantity') }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
