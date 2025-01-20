@extends('layouts.superAdmin')

@section('content')
<div class="container mx-auto my-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <div class="mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">Detail Invoice {{ $invoice->invoice_number }}</h2>
        </div>

        <!-- Sales Order Information -->
        <div class="mb-4">
            <h3 class="text-xl font-semibold text-gray-700">Sales Order</h3>
            <p class="text-sm text-gray-600">Pelanggan: {{ $invoice->salesOrder->customer_name }}</p>
            <p class="text-sm text-gray-600">Tipe Pembayaran: {{ $invoice->salesOrder->payment_type }}</p>
            <p class="text-sm text-gray-600">Jadwal Kirim: {{ \Carbon\Carbon::parse($salesOrder->jadwalKirim->delivery_date ?? 'N/A')->locale('id')->isoFormat('D MMMM YYYY') }}</p>
        </div>
        
        <table class="w-full table-auto border-collapse">
            <tbody>
                <tr class="border-b border-gray-300">
                    <th class="text-left py-2 px-4 text-sm font-medium text-gray-600">Diskon</th>
                    <td class="py-2 px-4 text-sm text-gray-800">
                        @if($invoice->discount == 0)
                            Harga Normal
                        @else
                            {{ number_format($invoice->discount, 2, ',', '.') }}
                        @endif
                    </td>
                </tr>
                <tr class="border-b border-gray-300">
                    <th class="text-left py-2 px-4 text-sm font-medium text-gray-600">Uang Muka</th>
                    <td class="py-2 px-4 text-sm text-gray-800">Rp. {{ number_format($invoice->down_payment, 2, ',', '.') }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                    <th class="text-left py-2 px-4 text-sm font-medium text-gray-600">PPN</th>
                    <td class="py-2 px-4 text-sm text-gray-800">
                        @if($invoice->vat == 0)
                            Harga Sebelum Pajak
                        @else
                            {{ number_format($invoice->vat, 2, ',', '.') }}%
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Surat Jalan (Jadwal Kirim) Information -->
        <div class="mt-6">
            <h3 class="text-xl font-semibold text-gray-700">Surat Jalan</h3>
            <div class="flex space-x-4 overflow-x-auto">
                @foreach($invoice->salesOrder->suratJalans as $suratJalan)
                <div class="bg-white p-4 rounded-lg shadow-md w-72">
                    <h4 class="text-lg font-semibold text-gray-600">No. Surat Jalan: {{ $suratJalan->no_surat_jalan }}</h4>
                    <p class="text-sm text-gray-600">Tanggal Pengiriman: {{ \Carbon\Carbon::parse($suratJalan->tanggal_pengiriman)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    <p class="text-sm text-gray-600">Plat Angkutan: {{ $suratJalan->plat_angkutan }}</p>
                    <p class="text-sm text-gray-600">Jumlah Barang: {{ $suratJalan->suratJalanDetails->sum('quantity') }}</p>
                </div>
                @endforeach
            </div>
        </div>
        

        <div class="mt-6 text-right">
            <a href="{{ route('superAdmin.invoice.generatePDF', $invoice->id) }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Download PDF</a>
            <a href="{{ route('superAdmin.invoice.generateXLS', $invoice->id) }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 ml-4">Download XLS</a>
            <a href="{{ route('superAdmin.invoice.index') }}" class="inline-block px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Kembali</a>
        </div>
    </div>
</div>
@endsection
