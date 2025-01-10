@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6">Daftar Pengguna</h1>
        <a href="{{ route('admin.users.create') }}" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg mb-4 hover:bg-blue-600 transition duration-300" onclick="konfirmasiBuat(event)">
            Tambah Pengguna
        </a>

        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full table-auto text-sm text-gray-700">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="py-3 px-6 font-medium text-gray-700">ID</th>
                        <th class="py-3 px-6 font-medium text-gray-700">Nama</th>
                        <th class="py-3 px-6 font-medium text-gray-700">Email</th>
                        <th class="py-3 px-6 font-medium text-gray-700">Role</th>
                        <th class="py-3 px-6 font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b hover:bg-gray-50 transition duration-200">
                            <td class="py-3 px-6">{{ $user->id }}</td>
                            <td class="py-3 px-6">{{ $user->name }}</td>
                            <td class="py-3 px-6">{{ $user->email }}</td>
                            <td class="py-3 px-6">{{ $user->role }}</td>
                            <td class="py-3 px-6">
                                @if ($user->role !== 'super-admin')
                                    <!-- Edit Button for non-super-admin users -->
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-yellow-500 hover:text-yellow-600 mr-3 transition duration-300" onclick="konfirmasiEdit(event, {{ $user->id }})">Edit</a>
                                    
                                    <!-- Delete Button for non-super-admin users -->
                                    <form id="form-hapus-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="konfirmasiHapus({{ $user->id }})" class="text-red-500 hover:text-red-600 transition duration-300">Delete</button>
                                    </form>
                                @else
                                    <!-- Cannot Edit or Delete Super Admin -->
                                    <span class="text-red-500">Cannot edit or delete Super Admin</span>
                                @endif
                            </td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- SweetAlert2 Script -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // SweetAlert for create confirmation
            function konfirmasiBuat(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Apakah Anda ingin membuat pengguna baru?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('admin.users.create') }}";
                    }
                });
            }

            // SweetAlert for edit confirmation
            function konfirmasiEdit(event, userId) {
                event.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Apakah Anda ingin mengedit pengguna ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ url('admin/users') }}/" + userId + "/edit";
                    }
                });
            }

            // SweetAlert for delete confirmation
            function konfirmasiHapus(userId) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Apakah Anda ingin menghapus pengguna ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-hapus-' + userId).submit();
                    }
                });
            }
        </script>
@endsection
