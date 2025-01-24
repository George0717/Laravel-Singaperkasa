@extends('layouts.superAdmin')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-xl font-bold mb-4 text-gray-800">Detail Sales Order</h1>

        <div class="bg-white shadow rounded-lg p-4">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Nama Pelanggan:</span>
                    <p class="text-gray-800">{{ $salesOrder->customer_name }}</p>
                </div>
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Nama Sales:</span>
                    <p class="text-gray-800">{{ $salesOrder->nama_sales }}</p>
                </div>
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Jatuh Tempo:</span>
                    <p class="text-gray-800">{{ $salesOrder->due_date->translatedFormat('j F Y') }}</p>
                </div>
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Nomor SO:</span>
                    <p class="text-gray-800">{{ $salesOrder->so_number }}</p>
                </div>
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Diskon:</span>
                    <p class="text-gray-800">{{ number_format($salesOrder->discount, 0, ',', '.') }} {{ $salesOrder->discount_type }} %</p>
                </div>
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Uang Muka:</span>
                    <p class="text-gray-800">{{ 'Rp ' . number_format($salesOrder->down_payment, 0, ',', '.') }}</p>
                </div>
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Pajak:</span>
                    <p class="text-gray-800">{{ number_format($salesOrder->vat, 0, ',', '.') }} %</p>
                </div>
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Grand Total:</span>
                    <p class="text-gray-800">{{ 'Rp ' . number_format($salesOrder->grand_total, 0, ',', '.') }}</p>
                </div>
                <div class="text-sm">
                    <span class="font-semibold text-gray-600">Jenis Pembayaran:</span>
                    <p class="text-gray-800">{{ $salesOrder->payment_type }}</p>
                </div>
                <div class="text-sm sm:col-span-2">
                    <span class="font-semibold text-gray-600">Foto PO:</span>
                    @if($salesOrder->po_photo)
                        <button class="text-blue-500 hover:underline mt-2" onclick="openModal()">Lihat Foto</button>
                        
                        <!-- Modal -->
                        <div id="photoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" onclick="closeModal(event)">
                            <div class="bg-white rounded-lg shadow-lg p-4 max-w-lg w-full relative" onclick="event.stopPropagation()">
                                <img src="{{ asset('storage/' . $salesOrder->po_photo) }}" alt="Foto PO" 
                                     class="w-[400px] h-[200px] object-cover rounded-lg">
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada foto tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <h2 class="text-lg font-bold mt-8 mb-4 text-gray-800">Detail Item</h2>

        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 py-2 text-gray-700">Nama Item</th>
                        <th class="px-4 py-2 text-gray-700">Per</th>
                        <th class="px-4 py-2 text-gray-700">Harga</th>
                        <th class="px-4 py-2 text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesOrder->details as $detail)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-800">{{ $detail->item_name }}</td>
                            <td class="px-4 py-2 text-gray-800">{{ $detail->quantity }}</td>
                            <td class="px-4 py-2 text-gray-800">{{ 'Rp ' . number_format($detail->price, 0, ',', '.') }}/{{ $detail->per }}</td>
                            <td class="px-4 py-2 text-gray-800">{{ 'Rp ' . number_format($detail->quantity * $detail->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('superAdmin.SalesOrders.index') }}" class="text-sm text-gray-700 hover:underline">Kembali ke Daftar</a>
            <a href="{{ route('superAdmin.SalesOrders.printPDF', $salesOrder->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Cetak PDF</a>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('photoModal').classList.remove('hidden');
        }

        function closeModal(event) {
            if (event.target.id === 'photoModal') {
                document.getElementById('photoModal').classList.add('hidden');
            }
        }
    </script>
@endsection
