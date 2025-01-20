<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminInvoiceController;
use App\Http\Controllers\AdminJadwalKirimController;
use App\Http\Controllers\AdminSalesOrderController;
use App\Http\Controllers\AdminSuratJalanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JadwalKirimController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockBarangController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminInvoiceController;
use App\Http\Controllers\SuperAdminJadwalKirimController;
use App\Http\Controllers\SuperAdminSalesOrderController;
use App\Http\Controllers\SuperAdminSuratJalanController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\UserManagementAdminController;
use App\Http\Controllers\UserManagementController;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

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

Route::get('/', function () {
    return response()
        ->view(['auth.login'])
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->middleware('guest', 'role.redirect');

session(['last_page' => url()->previous()]);

Route::get('/dashboard', function () {
    return view('pages.dashboard.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Route for the getting the data feed

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/dashboard/fintech', [DashboardController::class, 'fintech'])->name('fintech');

    

    // Rute untuk super-admin (mengelola semua pengguna)
    Route::middleware(['auth', 'role:super-admin'])->prefix('super-admin')->group(function () {
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
        Route::post('/superAdmin/sales_order/{id}/restore', [SuperAdminSalesOrderController::class, 'restore'])->name('superAdmin.salesOrders.restore');


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
        Route::post('/superAdmin/suratJalan/store', [SuperAdminSuratJalanController::class, 'store'])->name('superAdmin.suratJalan.store');
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

        // Halaman untuk melihat semua pengguna
        Route::get('superAdmin/users', [UserManagementController::class, 'index'])->name('superAdmin.users.index');
        Route::get('superAdmin/users/create', [UserManagementController::class, 'create'])->name('superAdmin.users.create');
        Route::post('superAdmin/users/store', [UserManagementController::class, 'store'])->name('superAdmin.users.store');
        Route::get('superAdmin/users/{id}/edit', [UserManagementController::class, 'edit'])->name('superAdmin.users.edit');
        Route::put('superAdmin/users/{id}', [UserManagementController::class, 'update'])->name('superAdmin.users.update');
        Route::delete('superAdmin/users/{id}', [UserManagementController::class, 'destroy'])->name('superAdmin.users.destroy');

        Route::get('superAdmin/stockBarang', [StockBarangController::class, 'index'])->name('superAdmin.stockBarang.index');
        Route::get('superAdmin/stockBarang/create', [StockBarangController::class, 'create'])->name('superAdmin.stockBarang.create');
        Route::post('superAdmin/stockBarang/store', [StockBarangController::class, 'store'])->name('superAdmin.stockBarang.store');
        Route::get('superAdmin/stockBarang/{id}/edit', [StockBarangController::class, 'edit'])->name('superAdmin.stockBarang.edit');
        Route::put('superAdmin/stockBarang/{id}', [StockBarangController::class, 'update'])->name('superAdmin.stockBarang.update');
        Route::delete('superAdmin/stockBarang/{id}', [StockBarangController::class, 'destroy'])->name('superAdmin.stockBarang.destroy');

        Route::get('/superAdmin/riwayat', [ActivityLogController::class, 'index'])->name('superAdmin.riwayat.index');
        Route::get('/superAdmin/riwayat/{id}', [ActivityLogController::class, 'show'])->name('superAdmin.riwayat.show');


    });

    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/users/create', [AdminController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users/store', [AdminController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{id}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{id}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

        // Sales Order
        Route::get('/admin/sales_orders', [AdminSalesOrderController::class, 'index'])->name('admin.SalesOrders.index');
        Route::get('/admin/sales_orders/create', [AdminSalesOrderController::class, 'create'])->name('admin.salesOrders.create');
        Route::post('/admin/sales_orders', [AdminSalesOrderController::class, 'store'])->name('admin.salesOrders.store');
        Route::get('/admin/sales_orders/{salesOrder}', [AdminSalesOrderController::class, 'show'])->name('admin.salesOrders.show');
        Route::get('/admin/sales_orders/{salesOrder}/edit', [AdminSalesOrderController::class, 'edit'])->name('admin.salesOrders.edit');
        Route::put('/admin/sales_orders/{salesOrder}', [AdminSalesOrderController::class, 'update'])->name('admin.salesOrders.update');
        Route::delete('/admin/sales_orders/{salesOrder}', [AdminSalesOrderController::class, 'destroy'])->name('admin.salesOrders.destroy');
        Route::get('/admin/sales_orders/{salesOrder}/print-pdf', [AdminSalesOrderController::class, 'printPDF'])->name('admin.SalesOrders.printPDF');
        Route::get('/admin/dashboard', [AdminSalesOrderController::class, 'dashboard'])->name('admin.SalesOrders.dashboard');

        // Jadwal Kirim
        Route::get('/admin/jadwalKirim', [AdminJadwalKirimController::class, 'index'])->name('admin.JadwalKirim.index');
        Route::get('/admin/jadwalKirim/create', [AdminJadwalKirimController::class, 'create'])->name('admin.jadwalKirim.create');
        Route::post('/admin/jadwalKirim', [AdminJadwalKirimController::class, 'store'])->name('admin.jadwalKirim.store');
        Route::get('/admin/jadwalKirim/{jadwalKirim}', [AdminJadwalKirimController::class, 'show'])->name('admin.jadwalKirim.show');
        Route::get('/admin/jadwalKirim/{jadwalKirim}/edit', [AdminJadwalKirimController::class, 'edit'])->name('admin.jadwalKirim.edit');
        Route::put('/admin/jadwalKirim/{jadwalKirim}', [AdminJadwalKirimController::class, 'update'])->name('admin.jadwalKirim.update');
        Route::delete('/admin/jadwalKirim/{jadwalKirim}', [AdminJadwalKirimController::class, 'destroy'])->name('admin.jadwalKirim.destroy');
        Route::get('/admin/pdf/generate/jadwalKirim/{jadwalKirim}', [AdminJadwalKirimController::class, 'printPDF'])->name('admin.pdf.generate');
        Route::get('/admin/sales-order-details', [AdminJadwalKirimController::class, 'showSalesOrderDetails'])->name('admin.salesOrder.details');

        // Surat Jalan Routes
        Route::get('/admin/suratJalan', [AdminSuratJalanController::class, 'index'])->name('admin.suratJalan.index');
        Route::get('/admin/suratJalan/create', [AdminSuratJalanController::class, 'create'])->name('admin.suratJalan.create');
        Route::post('/admin/suratJalan', [AdminSuratJalanController::class, 'store'])->name('admin.suratJalan.store');
        Route::get('/admin/suratJalan/{suratJalan}', [AdminSuratJalanController::class, 'show'])->name('admin.suratJalan.show');
        Route::get('/suratJalan/{suratJalan}/edit', [AdminSuratJalanController::class, 'edit'])->name('admin.suratJalan.edit');
        Route::put('/admin/suratJalan/{suratJalan}', [AdminSuratJalanController::class, 'update'])->name('admin.suratJalan.update');
        Route::delete('/admin/suratJalan/{suratJalan}', [AdminSuratJalanController::class, 'destroy'])->name('admin.suratJalan.destroy');
        Route::get('/admin/pdf/generate/suratJalan/{suratJalan}', [AdminSuratJalanController::class, 'generatePDF'])->name('admin.suratJalan.generate');

        // Invoice
        Route::get('/admin/invoice', [AdminInvoiceController::class, 'index'])->name('admin.invoice.index');
        Route::get('/admin/invoice/create', [AdminInvoiceController::class, 'create'])->name('admin.invoice.create');
        Route::post('/admin/invoice', [AdminInvoiceController::class, 'store'])->name('admin.invoice.store');
        Route::get('/admin/invoice/{invoice}', [AdminInvoiceController::class, 'show'])->name('admin.invoice.show');
        Route::get('/admin/invoice/{invoice}/edit', [AdminInvoiceController::class, 'edit'])->name('admin.invoice.edit');
        Route::put('/admin/invoice/{invoice}', [AdminInvoiceController::class, 'update'])->name('admin.invoice.update');
        Route::delete('/admin/invoice/{invoice}', [AdminInvoiceController::class, 'destroy'])->name('admin.invoice.destroy');
        Route::get('/admin/pdf/generate/invoice/{invoice}', [AdminInvoiceController::class, 'generatePDF'])->name('admin.invoice.generatePDF');
        Route::get('/admin/invoice/{invoice}/xls', [AdminInvoiceController::class, 'generateXLS'])->name('admin.invoice.generateXLS');
        Route::get('/admin/sales-order-data/{id}', [AdminInvoiceController::class, 'getSalesOrderData']);

        // Halaman untuk melihat semua pengguna
        Route::get('admin/users', [UserManagementAdminController::class, 'index'])->name('admin.users.index');
        Route::get('admin/users/create', [UserManagementAdminController::class, 'create'])->name('admin.users.create');
        Route::post('admin/users/store', [UserManagementAdminController::class, 'store'])->name('admin.users.store');
        Route::get('admin/users/{id}/edit', [UserManagementAdminController::class, 'edit'])->name('admin.users.edit');
        Route::put('admin/users/{id}', [UserManagementAdminController::class, 'update'])->name('admin.users.update');
        Route::delete('admin/users/{id}', [UserManagementAdminController::class, 'destroy'])->name('admin.users.destroy');
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
});




require __DIR__ . '/auth.php';
