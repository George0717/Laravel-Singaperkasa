@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div id="skeleton-loader" class="w-100">
        <!-- Skeleton loader placeholders -->
        <div class="skeleton-table mb-4"></div>
        <div class="skeleton-row"></div>
        <div class="skeleton-row"></div>
        <div class="skeleton-row"></div>
    </div>

    <div id="content" class="w-100" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-center">Invoices</h1>
            <a href="{{ route('invoice.create') }}" class="btn btn-success btn-lg shadow-sm">Create New Invoice</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle text-center mx-auto" style="width: 80%;">
                <thead class="table-dark">
                    <tr>
                        <th>Invoice Number</th>
                        <th>Customer</th>
                        <th>Subtotal</th>
                        <th>Grand Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invoices->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">Belum Ada Data</td>
                        </tr>
                    @else
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->salesOrder->customer_name }}</td>
                                <td>Rp {{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
                                <td>Rp {{ number_format($invoice->grand_total, 2, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('invoice.show', $invoice->id) }}" class="btn btn-info shadow-sm">View</a>
                                        <a href="{{ route('invoice.edit', $invoice->id) }}" class="btn btn-warning shadow-sm">Edit</a>
                                        <form action="{{ route('invoice.destroy', $invoice->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger shadow-sm">Delete</button>
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

<style>
    h1 {
        color: #158843;
        font-size: 2.5rem;
    }

    .table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Add spacing between table rows */
    .table td, .table th {
        padding: 12px;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
        transform: scale(1.01);
        transition: all 0.3s ease;
    }

    .btn {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Center the container and make sure table is not too wide */
    .container {
        max-width: 1200px;
    }

    /* Skeleton loader styles */
    .skeleton-table, .skeleton-row {
        background: linear-gradient(-90deg, #f0f0f0 0%, #e0e0e0 50%, #f0f0f0 100%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 10px;
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

<script>
    // Simulate a loading delay for skeleton animation
    window.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.getElementById('skeleton-loader').style.display = 'none';
            document.getElementById('content').style.display = 'block';
        }, 1500); // Adjust time as needed
    });
</script>
@endsection
