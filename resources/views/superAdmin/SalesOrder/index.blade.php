@extends('layouts.superAdmin')
@section('title', 'Sales Order')
@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Pesanan Penjualan</h1>
    <a href="{{ route('superAdmin.salesOrders.create') }}" class="btn btn-primary mb-4">Buat Pesanan
        Penjualan Baru</a>

    <!-- Search Inputs -->
    <div class="mb-4 flex items-center space-x-2">
        <input type="text" id="search-name" class="form-input w-full mb-2"
            placeholder="Cari Berdasarkan Nama Pelanggan">
        <input type="date" id="search-date" class="form-input w-full" placeholder="Cari Berdasarkan Tanggal PO">
        <button id="reset-button" class="btn btn- mb-2">Reset</button>
    </div>

    <table class="min-w-full divide-y divide-gray-200 table-responsive overflow-x-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                    Pelanggan</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor SO
                </th>

                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pemesanan
                </th>

                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="sales-orders-table">
            {{-- @dd($salesOrders); --}}
            @foreach ($salesOrders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center"
                    data-name="{{ $order->customer_name }}">{{ $order->customer_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $order->so_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <!-- Dropdown button -->
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="actionsDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Aksi
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
                            <li><a class="dropdown-item"
                                    href="{{ route('superAdmin.salesOrders.show', $order) }}">Lihat</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('superAdmin.salesOrders.edit', $order) }}">Edit</a></li>
                            <li>
                                <form action="{{ route('superAdmin.salesOrders.destroy', $order) }}" method="POST"
                                    class="d-inline" id="delete-form-{{ $order->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"
                                        onclick="confirmDelete(event, {{ $order->id }})">Hapus</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#search-name, #search-date').on('keyup', function() {
            var nameQuery = $('#search-name').val().toLowerCase();
            var dateQuery = $('#search-date').val().toLowerCase();

            $('#sales-orders-table tr').each(function() {
                var name = $(this).find('td[data-name]').text().toLowerCase();
                var date = $(this).find('td[data-date]').text().toLowerCase();

                var nameMatch = name.indexOf(nameQuery) > -1;
                var dateMatch = date.indexOf(dateQuery) > -1;

                $(this).toggle(nameMatch && dateMatch);
            });
        });
    });

    $(document).ready(function() {
    function filterSalesOrders() {
        var nameQuery = $('#search-name').val().toLowerCase();
        var dateQuery = $('#search-date').val(); // Directly use the date format

        $('#sales-orders-table tr').each(function() {
            var name = $(this).find('td[data-name]').text().toLowerCase();
            var date = $(this).find('td[data-date]').attr('data-date'); // Get the actual date attribute

            var nameMatch = name.indexOf(nameQuery) > -1;
            var dateMatch = date === dateQuery; // Exact match for date

            $(this).toggle(nameMatch && dateMatch);
        });
    }

    $('#filter-button').on('click', function() {
        filterSalesOrders();
    });

    $('#reset-button').on('click', function() {
        $('#search-name').val('');
        $('#search-date').val('');
        $('#sales-orders-table tr').show();
    });

    $('#search-name, #search-date').on('keyup change', function() {
        filterSalesOrders();
    });
});


    function confirmDelete(event, orderId) {
    event.preventDefault();
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: "Apakah Anda yakin ingin menghapus pesanan penjualan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + orderId).submit();
        }
    });
}

</script>
@endsection