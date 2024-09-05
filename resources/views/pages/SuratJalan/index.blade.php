@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Surat Jalan</h1>
    <a href="{{ route('suratJalan.create') }}" class="btn btn-primary mb-4" onclick="konfirmasiBuat(event)">Buat Surat
        Jalan Baru</a>

    <!-- Pencarian -->
    <div class="mb-4">
        <form method="GET" action="{{ route('suratJalan.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="cari-customer" class="block text-sm font-medium text-gray-700">Nama Customer</label>
                <input type="text" name="customer_name" id="cari-customer"
                    class="form-input mt-1 block w-full border-gray-300 rounded-md"
                    placeholder="Cari berdasarkan Nama Customer" value="{{ request('customer_name') }}">
            </div>
            <div>
                <label for="cari-tanggal" class="block text-sm font-medium text-gray-700">Tanggal Pengiriman</label>
                <input type="date" name="delivery_date" id="cari-tanggal"
                    class="form-input mt-1 block w-full border-gray-300 rounded-md"
                    placeholder="Cari berdasarkan Tanggal Pengiriman" value="{{ request('delivery_date') }}">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="btn btn-primary w-full">Cari</button>
                <a href="{{ route('suratJalan.index') }}" id="tombol-reset" class="btn btn-secondary w-full">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                        Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor
                        Surat Jalan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        Pengiriman</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="tabel-surat-jalan">
                @forelse ($suratJalans as $suratJalan)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                        data-name="{{ $suratJalan->salesOrder->customer_name }}">{{
                        $suratJalan->salesOrder->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $suratJalan->no_surat_jalan }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                        \Carbon\Carbon::parse($suratJalan->tanggal_pengiriman)->translatedFormat('d F Y') }}</td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('suratJalan.show', $suratJalan) }}"
                            class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">Lihat</a>
                        <a href="{{ route('suratJalan.edit', $suratJalan) }}"
                            class="text-green-600 hover:text-green-900 transition duration-300 ease-in-out ml-4"
                            onclick="konfirmasiEdit(event, {{ $suratJalan->id }})">Edit</a>
                        <a href="{{ route('suratJalan.kirim', $suratJalan) }}"
                            class="text-yellow-600 hover:text-yellow-900 transition duration-300 ease-in-out ml-4"
                            onclick="konfirmasiKirim(event, {{ $suratJalan->id }})">Kirim</a>
                        <form action="{{ route('suratJalan.destroy', $suratJalan) }}" method="POST" class="inline"
                            id="form-hapus-{{ $suratJalan->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="konfirmasiHapus({{ $suratJalan->id }})"
                                class="text-red-600 hover:text-red-900 transition duration-300 ease-in-out ml-4">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-gray-500">Tidak ada data ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
        $('#cari-customer, #cari-tanggal').on('keyup', function() {
            var queryCustomer = $('#cari-customer').val().toLowerCase();
            var queryTanggal = $('#cari-tanggal').val().toLowerCase();

            $('#tabel-surat-jalan tr').each(function() {
                var namaCustomer = $(this).find('td[data-name]').text().toLowerCase();
                var tanggalPengiriman = $(this).find('td[data-date]').text().toLowerCase();

                var cocokNama = namaCustomer.indexOf(queryCustomer) > -1;
                var cocokTanggal = tanggalPengiriman.indexOf(queryTanggal) > -1;

                $(this).toggle(cocokNama && cocokTanggal);
            });
        });
    });

    function konfirmasiBuat(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Apakah Anda ingin membuat Surat Jalan baru?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('suratJalan.create') }}";
            }
        });
    }

    function konfirmasiKirim(event, suratJalanId) {
    event.preventDefault();
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Apakah Anda ingin mengirim Surat Jalan ini?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, kirim!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ url('suratJalan') }}/" + suratJalanId + "/terkirim";
        }
    });
}

    function konfirmasiEdit(event, suratJalanId) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Apakah Anda ingin mengedit Surat Jalan ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('surat_jalan') }}/" + suratJalanId + "/edit";
            }
        });
    }

    function konfirmasiHapus(suratJalanId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Apakah Anda ingin menghapus Surat Jalan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-hapus-' + suratJalanId).submit();
            }
        });
    }
</script>
@endsection