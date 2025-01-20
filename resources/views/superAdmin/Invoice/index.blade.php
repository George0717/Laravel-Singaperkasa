@extends('layouts.superAdmin')
@section('title', 'Invoice')
@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Skeleton Loader -->
    <div id="skeleton-loader" class="w-full mb-4">
        <div class="skeleton-table mb-4"></div>
        <div class="skeleton-row"></div>
        <div class="skeleton-row"></div>
        <div class="skeleton-row"></div>
    </div>

    <!-- Main Content -->
    <div id="content" class="hidden w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-semibold text-green-600">Invoices</h1>
            <button id="create-button"
                class="text-white py-2 px-4 rounded shadow-sm bg-green-500 hover:bg-green-600 transition">
                Create New Invoice
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-center">Nomor Invoice</th>
                        <th class="px-6 py-3 text-center">Nama Customer</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @if($invoices->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 py-6">No invoices available</td>
                    </tr>
                    @else
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-100">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 text-center">{{ $invoice->invoice_number
                            }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-center">{{ $invoice->salesOrder->customer_name
                            }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2 justify-center">
                                <a href="{{ route('superAdmin.invoice.show', ['invoice' => $invoice->id]) }}"
                                    class="text-white py-2 px-4 rounded bg-blue-500 hover:bg-blue-600 transition inline-block">
                                    View
                                </a>

                                <!-- Tombol Delete -->
                                <button onclick="confirmDelete('{{ $invoice->id }}', '{{ $invoice->invoice_number }}')"
                                    class="text-white py-2 px-4 rounded bg-red-500 hover:bg-red-600 transition">
                                    Delete
                                </button>

                                <!-- Form Delete -->
                                <form id="delete-form-{{ $invoice->id }}"
                                    action="{{ route('superAdmin.invoice.destroy', $invoice->id) }}" method="POST"
                                    class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Skeleton loader styles -->
<style>
    .skeleton-table,
    .skeleton-row {
        background: linear-gradient(-90deg, #f0f0f0 0%, #e0e0e0 50%, #f0f0f0 100%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 8px;
    }

    .skeleton-table {
        height: 50px;
        width: 100%;
        margin-bottom: 10px;
    }

    .skeleton-row {
        height: 40px;
        width: 100%;
        margin-bottom: 10px;
    }

    @keyframes skeleton-loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Show a SweetAlert for View and Edit actions

    // Confirm deletion with SweetAlert
    function confirmDelete(id, invoiceNumber) {
        Swal.fire({
            title: `Delete Invoice ${invoiceNumber}`,
            text: `Are you sure you want to delete invoice: ${invoiceNumber}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the deletion form
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    // Alert for Create action
    document.getElementById('create-button').addEventListener('click', function () {
        Swal.fire({
            title: 'Create Invoice',
            text: 'You are about to create a new invoice.',
            icon: 'success',
            confirmButtonText: 'Proceed',
            confirmButtonColor: '#158843'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('superAdmin.invoice.create') }}";
            }
        });
    });

    // Simulate a loading delay for skeleton animation
    window.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.getElementById('skeleton-loader').style.display = 'none';
            document.getElementById('content').style.display = 'block';
        }, 1500); // Adjust time as needed
    });
</script>
@endsection