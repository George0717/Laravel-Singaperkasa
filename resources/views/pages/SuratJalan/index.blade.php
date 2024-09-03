@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Surat Jalans</h1>
    <a href="{{ route('suratJalan.create') }}" class="btn btn-primary mb-4" onclick="confirmCreate(event)">Create New Surat Jalan</a>

    <!-- Search Inputs -->
    <div class="mb-4 flex items-center space-x-2">
        <form method="GET" action="{{ route('suratJalan.index') }}" class="flex w-full">
            <input type="text" name="customer_name" id="search-customer" class="form-input w-full mb-2" placeholder="Search by Customer Name" value="{{ request('customer_name') }}">
            <input type="date" name="delivery_date" id="search-date" class="form-input w-full" placeholder="Search by Delivery Date" value="{{ request('delivery_date') }}">
            <button type="submit" class="btn btn-primary mb-2">Search</button>
            <a href="{{ route('suratJalan.index') }}" id="reset-button" class="btn btn-secondary mb-2 ml-2">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <table class="min-w-full divide-y divide-gray-200 table-responsive overflow-x-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surat Jalan Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="surat-jalan-table">
            @forelse ($suratJalans as $suratJalan)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-name="{{ $suratJalan->salesOrder->customer_name }}">{{ $suratJalan->salesOrder->customer_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $suratJalan->no_surat_jalan }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-date="{{ $suratJalan->tanggal_pengiriman->format('Y-m-d') }}">{{ $suratJalan->tanggal_pengiriman->translatedFormat('j F Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('suratJalan.show', $suratJalan) }}" class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">View</a>
                    <a href="{{ route('suratJalan.edit', $suratJalan) }}" class="text-green-600 hover:text-green-900 transition duration-300 ease-in-out ml-4" onclick="confirmEdit(event, {{ $suratJalan->id }})">Edit</a>
                    <form action="{{ route('suratJalan.destroy', $suratJalan) }}" method="POST" class="inline" id="delete-form-{{ $suratJalan->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $suratJalan->id }})" class="text-red-600 hover:text-red-900 transition duration-300 ease-in-out ml-4">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-gray-500">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $suratJalans->appends(request()->query())->links() }}
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#search-customer, #search-date').on('keyup', function() {
            var customerQuery = $('#search-customer').val().toLowerCase();
            var dateQuery = $('#search-date').val().toLowerCase();

            $('#surat-jalan-table tr').each(function() {
                var customerName = $(this).find('td[data-name]').text().toLowerCase();
                var deliveryDate = $(this).find('td[data-date]').text().toLowerCase();

                var nameMatch = customerName.indexOf(customerQuery) > -1;
                var dateMatch = deliveryDate.indexOf(dateQuery) > -1;

                $(this).toggle(nameMatch && dateMatch);
            });
        });
    });

    function confirmCreate(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to create a new Surat Jalan?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('suratJalan.create') }}";
            }
        });
    }

    function confirmEdit(event, suratJalanId) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to edit this Surat Jalan?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('surat_jalan') }}/" + suratJalanId + "/edit";
            }
        });
    }

    function confirmDelete(suratJalanId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this Surat Jalan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + suratJalanId).submit();
            }
        });
    }
</script>
@endsection
