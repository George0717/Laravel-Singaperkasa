@extends('layouts.app')
@section('title', 'Jadwal Kirim')
@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Jadwal Kirim</h1>
    <a href="{{ route('jadwalKirim.create') }}" class="btn btn-primary mb-4" onclick="confirmCreate(event)">Tambah Jadwal Kirim</a>

    <!-- Filter Inputs -->
    <div class="mb-4 flex flex-wrap items-center space-x-2">
        <div class="mb-2">
            <input type="text" id="search-customer" class="form-input" placeholder="Cari Nama Customer">
        </div>
        <div class="mb-2">
            <input type="text" id="search-so-number" class="form-input" placeholder="Cari Nomor SO">
        </div>
        <div class="mb-2">
            <input type="date" id="search-date" class="form-input" placeholder="Cari Tanggal Kirim">
        </div>
        <div class="mb-2">
            <button id="reset-button" class="btn btn-secondary">Reset</button>
        </div>
    </div>

    <table class="min-w-full divide-y divide-gray-200 table-responsive overflow-x-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor SO</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kirim</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="jadwal-kirims-table">
            @foreach ($jadwalKirims as $jadwalKirim)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-customer="{{ strtolower($jadwalKirim->salesOrder->customer_name) }}">{{ $jadwalKirim->salesOrder->customer_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-so="{{ strtolower($jadwalKirim->salesOrder->so_number) }}">{{ $jadwalKirim->salesOrder->so_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-date="{{ $jadwalKirim->delivery_date }}">{{ \Carbon\Carbon::parse($jadwalKirim->delivery_date)->translatedFormat('j F Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $jadwalKirim->keterangan }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('jadwalKirim.show', $jadwalKirim->id) }}" class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">View</a>
                    <a href="{{ route('jadwalKirim.edit', $jadwalKirim->id) }}" class="text-green-600 hover:text-green-900 transition duration-300 ease-in-out ml-4" onclick="confirmEdit(event, {{ $jadwalKirim->id }})">Edit</a>
                    <form action="{{ route('jadwalKirim.destroy', $jadwalKirim->id) }}" method="POST" class="inline" id="delete-form-{{ $jadwalKirim->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $jadwalKirim->id }})" class="text-red-600 hover:text-red-900 transition duration-300 ease-in-out ml-4">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Fungsi Filter
        function filterJadwalKirims() {
            var customerQuery = $('#search-customer').val().toLowerCase();
            var soQuery = $('#search-so-number').val().toLowerCase();
            var dateQuery = $('#search-date').val();

            $('#jadwal-kirims-table tr').each(function() {
                var customer = $(this).find('td[data-customer]').text().toLowerCase();
                var so = $(this).find('td[data-so]').text().toLowerCase();
                var date = $(this).find('td[data-date]').attr('data-date');

                var customerMatch = customer.includes(customerQuery);
                var soMatch = so.includes(soQuery);
                var dateMatch = date.includes(dateQuery) || dateQuery === '';

                $(this).toggle(customerMatch && soMatch && dateMatch);
            });
        }

        // Event Listener untuk Filter
        $('#search-customer, #search-so-number, #search-date').on('keyup change', function() {
            filterJadwalKirims();
        });

        // Event Listener untuk Reset
        $('#reset-button').on('click', function() {
            $('#search-customer').val('');
            $('#search-so-number').val('');
            $('#search-date').val('');
            $('#jadwal-kirims-table tr').show();
        });
    });

    // Konfirmasi Create
    function confirmCreate(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menambahkan jadwal kirim baru.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('jadwalKirim.create') }}";
            }
        });
    }

    // Konfirmasi Edit
    function confirmEdit(event, jadwalKirimId) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan mengedit jadwal kirim ini.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('jadwalKirim') }}/" + jadwalKirimId + "/edit";
            }
        });
    }

    // Konfirmasi Delete
    function confirmDelete(jadwalKirimId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menghapus jadwal kirim ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + jadwalKirimId).submit();
            }
        });
    }
</script>
@endsection
