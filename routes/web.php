<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalKirimController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
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

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/dashboard/fintech', [DashboardController::class, 'fintech'])->name('fintech');

// Sales Order Routes
Route::get('/sales_orders', [SalesOrderController::class, 'index'])->name('SalesOrders.index');
Route::get('/sales_orders/create', [SalesOrderController::class, 'create'])->name('salesOrders.create');
Route::post('/sales_orders', [SalesOrderController::class, 'store'])->name('salesOrders.store');
Route::get('/sales_orders/{salesOrder}', [SalesOrderController::class, 'show'])->name('salesOrders.show');
Route::get('/sales_orders/{salesOrder}/edit', [SalesOrderController::class, 'edit'])->name('salesOrders.edit');
Route::put('/sales_orders/{salesOrder}', [SalesOrderController::class, 'update'])->name('salesOrders.update');
Route::delete('/sales_orders/{salesOrder}', [SalesOrderController::class, 'destroy'])->name('salesOrders.destroy');
Route::get('/sales_orders/{salesOrder}/print-pdf', [SalesOrderController::class, 'printPDF'])->name('SalesOrders.printPDF');

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

});
