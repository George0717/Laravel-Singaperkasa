@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Sales Orders</h1>
    <a href="{{ route('salesOrders.create') }}" class="btn btn-primary mb-4" onclick="confirmCreate(event)">Create New Sales Order</a>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SO Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($salesOrders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->customer_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->so_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->po_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ 'Rp ' .
                    number_format($order->grand_total, 0, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('salesOrders.show', $order) }}"
                        class="text-blue-600 hover:text-blue-900 transition duration-300 ease-in-out">View</a>
                    <a href="{{ route('salesOrders.edit', $order) }}"
                        class="text-green-600 hover:text-green-900 transition duration-300 ease-in-out ml-4"
                        onclick="confirmEdit(event, {{ $order->id }})">Edit</a>
                    <form action="{{ route('salesOrders.destroy', $order) }}" method="POST" class="inline"
                        id="delete-form-{{ $order->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $order->id }})"
                            class="text-red-600 hover:text-red-900 transition duration-300 ease-in-out ml-4">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmCreate(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to create a new sales order?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('salesOrders.create') }}";
                }
            });
        }

        function confirmEdit(event, salesOrderId) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to edit this sales order?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Generate the URL with the salesOrderId
                window.location.href = "{{ url('sales_orders') }}/" + salesOrderId + "/edit";
            }
        });
    }


        function confirmDelete(orderId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this sales order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + orderId).submit();
                }
            });
        }
</script>
@endsection