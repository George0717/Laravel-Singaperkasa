@extends('layouts.admin')

@section('content')
<h1>Dashboard Sales Order</h1>

<!-- Filter Form -->
<form action="{{ route('superAdmin.SalesOrders.dashboard') }}" method="GET" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <!-- Filter Tahun -->
        <div>
            <label for="year" class="block text-sm font-medium text-gray-700">Tahun</label>
            <select name="year" id="year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        <!-- Filter Bulan -->
        <div>
            <label for="month" class="block text-sm font-medium text-gray-700">Bulan</label>
            <select name="month" id="month" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Semua Bulan</option>
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tanggal Mulai -->
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ request('start_date') }}">
        </div>

        <!-- Tanggal Akhir -->
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
            <input type="date" name="end_date" id="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ request('end_date') }}">
        </div>
    </div>

    <!-- Tombol Filter dan Reset -->
    <div class="flex space-x-4">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium text-sm rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Filter
        </button>
        <a href="{{ route('superAdmin.SalesOrders.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-700 font-medium text-sm rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Reset
        </a>
    </div>
</form>


<!-- Cards -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Total Sales Order</div>
            <div class="card-body">
                <h5 class="card-title">{{ $totalSalesOrders }}</h5>
                <p class="card-text">Jumlah total Sales Order yang telah dibuat.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Sales Order Bulan Ini</div>
            <div class="card-body">
                <h5 class="card-title">{{ $currentMonthSalesOrders }}</h5>
                <p class="card-text">Sales Order yang dibuat pada bulan ini.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Sales Order Tahun Ini</div>
            <div class="card-body">
                <h5 class="card-title">{{ $currentYearSalesOrders }}</h5>
                <p class="card-text">Sales Order yang dibuat pada tahun ini.</p>
            </div>
        </div>
    </div>
</div>

<!-- Grafik -->
<div class="mt-4">
    <canvas id="salesOrderChart"></canvas>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesOrderChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line', // Grafik garis
    data: {
        labels: @json($labels), // Labels dari data controller
        datasets: [{
            label: 'Jumlah Sales Order',
            data: @json($data), // Data jumlah sales order
            borderColor: '#158843',
            backgroundColor: 'rgba(21, 136, 67, 0.1)',
            borderWidth: 2,
            tension: 0,
            fill: false,
            pointBackgroundColor: '#158843',
            pointBorderColor: '#fff',
            pointRadius: 3,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            tooltip: {
                enabled: true,
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw}`;
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Bulan',
                    color: '#333',
                    font: {
                        size: 14
                    }
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Jumlah Sales Order',
                    color: '#333',
                    font: {
                        size: 14
                    }
                },
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection
