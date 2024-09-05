@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-5" style="color: #158843;">Stock Barang</h1>
    
    <!-- Dropdown untuk memilih Nomor SO -->
    <div class="row mb-4 justify-content-center">
        <div class="col-md-6">
            <div class="input-group">
                <select class="form-select" id="selectSO">
                    <option value="" selected>Pilih Nomor SO</option>
                    @foreach($salesOrders as $salesOrder)
                        <option value="{{ $salesOrder->id }}">{{ $salesOrder->so_number }}</option>
                    @endforeach
                </select>
                <span class="input-group-text" id="basic-addon2"><i class="bi bi-search"></i></span>
            </div>
        </div>
    </div>

    <!-- Tampilkan informasi setelah Nomor SO dipilih -->
    <div class="row" id="salesOrderInfo" style="display: none;">
        <div class="col-md-12">
            <!-- Informasi Sales Order -->
            <div class="card border-success shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Informasi Sales Order</h3>
                </div>
                <div class="card-body">
                    <p><strong>Nomor SO:</strong> <span id="soNumber" class="text-primary"></span></p>
                    <p><strong>Nama Customer:</strong> <span id="customerName"></span></p>
                    <p><strong>Alamat Customer:</strong> <span id="customerAddress"></span></p>
                    <p><strong>Tanggal PO:</strong> <span id="poDate"></span></p>
                </div>
            </div>

            <!-- Tabel untuk menampilkan barang yang dipesan -->
            <h3 class="text-center" style="color: #158843;">Detail Barang</h3>
            <table class="table table-hover table-bordered mt-4">
                <thead class="table-success">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah Stok</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <!-- Baris item akan diisi secara dinamis -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert ketika tidak ada data -->
    <div id="noDataAlert" class="alert alert-warning text-center" style="display: none;" role="alert">
        <strong>Data tidak ditemukan untuk Nomor SO ini.</strong>
    </div>
</div>

<script>
    document.getElementById('selectSO').addEventListener('change', function() {
        let selectedSO = this.value;

        if (selectedSO !== "") {
            document.getElementById('salesOrderInfo').style.display = 'block';
            document.getElementById('noDataAlert').style.display = 'none';  // Sembunyikan alert jika ada
            
            let salesOrders = @json($salesOrders);
            let selectedOrder = salesOrders.find(order => order.id == selectedSO);

            if (selectedOrder) {
                document.getElementById('soNumber').textContent = selectedOrder.so_number;
                document.getElementById('customerName').textContent = selectedOrder.customer_name;
                document.getElementById('customerAddress').textContent = selectedOrder.customer_address;
                
                // Convert date to readable format
                let poDate = new Date(selectedOrder.po_date);
                document.getElementById('poDate').textContent = poDate.toLocaleDateString();

                let itemsTableBody = document.getElementById('itemsTableBody');
                itemsTableBody.innerHTML = '';

                selectedOrder.details.forEach(function(item) {
                    let row = `
                        <tr>
                            <td>${item.item_name}</td>
                            <td>${item.quantity}</td>
                            <td>
                                <a href="{{ url('stock/show') }}/${item.id}" class="btn btn-outline-info btn-sm">Lihat Riwayat</a>
                            </td>
                        </tr>
                    `;
                    itemsTableBody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                document.getElementById('noDataAlert').style.display = 'block';
                document.getElementById('salesOrderInfo').style.display = 'none';
            }
        } else {
            document.getElementById('salesOrderInfo').style.display = 'none';
            document.getElementById('noDataAlert').style.display = 'none';
        }
    });
</script>
@endsection
