<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JadwalKirimController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminInvoiceController;
use App\Http\Controllers\SuperAdminJadwalKirimController;
use App\Http\Controllers\SuperAdminSalesOrderController;
use App\Http\Controllers\SuperAdminSuratJalanController;
use App\Http\Controllers\SuratJalanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');

Route::middleware(['auth', 'verified'])->group(function () {

    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/dashboard/fintech', [DashboardController::class, 'fintech'])->name('fintech');

    // Rute untuk super-admin (mengelola semua pengguna)
    Route::middleware(['auth', 'super-admin'])->group(function () {
        Route::get('/superAdmin/dashboard', [SuperAdminController::class, 'index'])->name('superAdmin.dashboard');
        Route::get('/superAdmin/users', [SuperAdminController::class, 'users'])->name('superAdmin.users');
        Route::get('/superAdmin/users/create', [SuperAdminController::class, 'create'])->name('superAdmin.users.create');
        Route::post('/superAdmin/users/store', [SuperAdminController::class, 'store'])->name('superAdmin.users.store');
        Route::get('/superAdmin/users/{id}/edit', [SuperAdminController::class, 'edit'])->name('superAdmin.users.edit');
        Route::put('/superAdmin/users/{id}', [SuperAdminController::class, 'update'])->name('superAdmin.users.update');
        Route::delete('/superAdmin/users/{id}', [SuperAdminController::class, 'destroy'])->name('superAdmin.users.destroy');

        // Sales Order
        Route::get('/superAdmin/sales_orders', [SuperAdminSalesOrderController::class, 'index'])->name('superAdmin.SalesOrders.index');
        Route::get('/superAdmin/sales_orders/create', [SuperAdminSalesOrderController::class, 'create'])->name('superAdmin.salesOrders.create');
        Route::post('/superAdmin/sales_orders', [SuperAdminSalesOrderController::class, 'store'])->name('superAdmin.salesOrders.store');
        Route::get('/superAdmin/sales_orders/{salesOrder}', [SuperAdminSalesOrderController::class, 'show'])->name('superAdmin.salesOrders.show');
        Route::get('/superAdmin/sales_orders/{salesOrder}/edit', [SuperAdminSalesOrderController::class, 'edit'])->name('superAdmin.salesOrders.edit');
        Route::put('/superAdmin/sales_orders/{salesOrder}', [SuperAdminSalesOrderController::class, 'update'])->name('superAdmin.salesOrders.update');
        Route::delete('/superAdmin/sales_orders/{salesOrder}', [SuperAdminSalesOrderController::class, 'destroy'])->name('superAdmin.salesOrders.destroy');
        Route::get('/superAdmin/sales_orders/{salesOrder}/print-pdf', [SuperAdminSalesOrderController::class, 'printPDF'])->name('superAdmin.SalesOrders.printPDF');
        Route::get('/superAdmin/dashboard', [SuperAdminSalesOrderController::class, 'dashboard'])->name('superAdmin.SalesOrders.dashboard');

        // Jadwal Kirim
        Route::get('/superAdmin/jadwalKirim', [SuperAdminJadwalKirimController::class, 'index'])->name('superAdmin.JadwalKirim.index');
        Route::get('/superAdmin/jadwalKirim/create', [SuperAdminJadwalKirimController::class, 'create'])->name('superAdmin.jadwalKirim.create');
        Route::post('/superAdmin/jadwalKirim', [SuperAdminJadwalKirimController::class, 'store'])->name('superAdmin.jadwalKirim.store');
        Route::get('/superAdmin/jadwalKirim/{jadwalKirim}', [SuperAdminJadwalKirimController::class, 'show'])->name('superAdmin.jadwalKirim.show');
        Route::get('/superAdmin/jadwalKirim/{jadwalKirim}/edit', [SuperAdminJadwalKirimController::class, 'edit'])->name('superAdmin.jadwalKirim.edit');
        Route::put('/superAdmin/jadwalKirim/{jadwalKirim}', [SuperAdminJadwalKirimController::class, 'update'])->name('superAdmin.jadwalKirim.update');
        Route::delete('/superAdmin/jadwalKirim/{jadwalKirim}', [SuperAdminJadwalKirimController::class, 'destroy'])->name('superAdmin.jadwalKirim.destroy');
        Route::get('/superAdmin/pdf/generate/jadwalKirim/{jadwalKirim}', [SuperAdminJadwalKirimController::class, 'printPDF'])->name('superAdmin.pdf.generate');
        Route::get('/superAdmin/sales-order-details', [SuperAdminJadwalKirimController::class, 'showSalesOrderDetails'])->name('superAdmin.salesOrder.details');

        // Surat Jalan Routes
        Route::get('/superAdmin/suratJalan', [SuperAdminSuratJalanController::class, 'index'])->name('superAdmin.suratJalan.index');
        Route::get('/superAdmin/suratJalan/create', [SuperAdminSuratJalanController::class, 'create'])->name('superAdmin.suratJalan.create');
        Route::post('/superAdmin/suratJalan', [SuperAdminSuratJalanController::class, 'store'])->name('superAdmin.suratJalan.store');
        Route::get('/superAdmin/suratJalan/{suratJalan}', [SuperAdminSuratJalanController::class, 'show'])->name('superAdmin.suratJalan.show');
        Route::get('/suratJalan/{suratJalan}/edit', [SuperAdminSuratJalanController::class, 'edit'])->name('superAdmin.suratJalan.edit');
        Route::put('/superAdmin/suratJalan/{suratJalan}', [SuperAdminSuratJalanController::class, 'update'])->name('superAdmin.suratJalan.update');
        Route::delete('/superAdmin/suratJalan/{suratJalan}', [SuperAdminSuratJalanController::class, 'destroy'])->name('superAdmin.suratJalan.destroy');
        Route::get('/superAdmin/pdf/generate/suratJalan/{suratJalan}', [SuperAdminSuratJalanController::class, 'generatePDF'])->name('superAdmin.suratJalan.generate');

        // Invoice
        Route::get('/superAdmin/invoice', [SuperAdminInvoiceController::class, 'index'])->name('superAdmin.invoice.index');
        Route::get('/superAdmin/invoice/create', [SuperAdminInvoiceController::class, 'create'])->name('superAdmin.invoice.create');
        Route::post('/superAdmin/invoice', [SuperAdminInvoiceController::class, 'store'])->name('superAdmin.invoice.store');
        Route::get('/superAdmin/invoice/{invoice}', [SuperAdminInvoiceController::class, 'show'])->name('superAdmin.invoice.show');
        Route::get('/superAdmin/invoice/{invoice}/edit', [SuperAdminInvoiceController::class, 'edit'])->name('superAdmin.invoice.edit');
        Route::put('/superAdmin/invoice/{invoice}', [SuperAdminInvoiceController::class, 'update'])->name('superAdmin.invoice.update');
        Route::delete('/superAdmin/invoice/{invoice}', [SuperAdminInvoiceController::class, 'destroy'])->name('superAdmin.invoice.destroy');
        Route::get('/superAdmin/pdf/generate/invoice/{invoice}', [SuperAdminInvoiceController::class, 'generatePDF'])->name('superAdmin.invoice.generatePDF');
        Route::get('/superAdmin/invoice/{invoice}/xls', [SuperAdminInvoiceController::class, 'generateXLS'])->name('superAdmin.invoice.generateXLS');
        Route::get('/superAdmin/sales-order-data/{id}', [SuperAdminInvoiceController::class, 'getSalesOrderData']);
    });

    // Sales Order Routes
    Route::get('/sales_orders', [SalesOrderController::class, 'index'])->name('SalesOrders.index');
    Route::get('/sales_orders/create', [SalesOrderController::class, 'create'])->name('salesOrders.create');
    Route::post('/sales_orders', [SalesOrderController::class, 'store'])->name('salesOrders.store');
    Route::get('/sales_orders/{salesOrder}', [SalesOrderController::class, 'show'])->name('salesOrders.show');
    Route::get('/sales_orders/{salesOrder}/edit', [SalesOrderController::class, 'edit'])->name('salesOrders.edit');
    Route::put('/sales_orders/{salesOrder}', [SalesOrderController::class, 'update'])->name('salesOrders.update');
    Route::delete('/sales_orders/{salesOrder}', [SalesOrderController::class, 'destroy'])->name('salesOrders.destroy');
    Route::get('/sales_orders/{salesOrder}/print-pdf', [SalesOrderController::class, 'printPDF'])->name('SalesOrders.printPDF');
    Route::get('/dashboard', [SalesOrderController::class, 'dashboard'])->name('SalesOrders.dashboard');


    // Jadwal Kirim Routes
    Route::get('/jadwalKirim', [JadwalKirimController::class, 'index'])->name('JadwalKirim.index');
    Route::get('/jadwalKirim/create', [JadwalKirimController::class, 'create'])->name('jadwalKirim.create');
    Route::post('/jadwalKirim', [JadwalKirimController::class, 'store'])->name('jadwalKirim.store');
    Route::get('/jadwalKirim/{jadwalKirim}', [JadwalKirimController::class, 'show'])->name('jadwalKirim.show');
    Route::get('/jadwalKirim/{jadwalKirim}/edit', [JadwalKirimController::class, 'edit'])->name('jadwalKirim.edit');
    Route::put('/jadwalKirim/{jadwalKirim}', [JadwalKirimController::class, 'update'])->name('jadwalKirim.update');
    Route::delete('/jadwalKirim/{jadwalKirim}', [JadwalKirimController::class, 'destroy'])->name('jadwalKirim.destroy');
    Route::get('/pdf/generate/jadwalKirim/{jadwalKirim}', [JadwalKirimController::class, 'printPDF'])->name('pdf.generate');
    Route::get('/sales-order-details', [JadwalKirimController::class, 'showSalesOrderDetails'])->name('salesOrder.details');

    // Surat Jalan Routes
    Route::get('/suratJalan', [SuratJalanController::class, 'index'])->name('suratJalan.index');
    Route::get('/suratJalan/create', [SuratJalanController::class, 'create'])->name('suratJalan.create');
    Route::post('/suratJalan', [SuratJalanController::class, 'store'])->name('suratJalan.store');
    Route::get('/suratJalan/{suratJalan}', [SuratJalanController::class, 'show'])->name('suratJalan.show');
    Route::get('/suratJalan/{suratJalan}/edit', [SuratJalanController::class, 'edit'])->name('suratJalan.edit');
    Route::put('/suratJalan/{suratJalan}', [SuratJalanController::class, 'update'])->name('suratJalan.update');
    Route::delete('/suratJalan/{suratJalan}', [SuratJalanController::class, 'destroy'])->name('suratJalan.destroy');
    Route::get('/pdf/generate/suratJalan/{suratJalan}', [SuratJalanController::class, 'generatePDF'])->name('suratJalan.generate');

    // Stock Barang
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{id}', [StockController::class, 'show'])->name('stock.show');

    // Invoice
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/invoice', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/invoice/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/invoice/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::put('/invoice/{invoice}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::get('/pdf/generate/invoice/{invoice}', [InvoiceController::class, 'generatePDF'])->name('invoice.generatePDF');
    Route::get('/invoice/{invoice}/xls', [InvoiceController::class, 'generateXLS'])->name('invoice.generateXLS');
    Route::get('/sales-order-data/{id}', [InvoiceController::class, 'getSalesOrderData']);
});
