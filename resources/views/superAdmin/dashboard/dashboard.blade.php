@extends('layouts.superAdmin')

@section('content')
<div class="container">
    <h1>Dashboard Sales Order</h1>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('SalesOrders.dashboard') }}">
        <div class="row">
            <div class="col-md-4">
                <label for="month">Bulan</label>
                <select name="month" id="month" class="form-control">
                    <option value="">Semua Bulan</option> <!-- Tambahkan opsi untuk semua bulan -->
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label for="year">Tahun</label>
                <select name="year" id="year" class="form-control">
                    @for($y = date('Y') - 5; $y <= date('Y'); $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
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
