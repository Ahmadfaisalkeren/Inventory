<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesDetailsController;
use App\Http\Controllers\SalessController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', fn() => redirect()->route('login'));

Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:superadmin'])->group(function (){
        Route::resource('inventories', InventoryController::class);
        Route::get('export/xlsx', [InventoryController::class, 'exportExcel'])->name('export.excel');
        Route::get('export/csv', [InventoryController::class, 'exportCSV'])->name('export.csv');
        Route::get('export/pdf', [InventoryController::class, 'exportPDF'])->name('export.pdf');
    });

    Route::group(['middleware' => 'role:superadmin,sales,manager'], function() {
        Route::resource('sales', SalesController::class);
        Route::resource('sales_details', SalesDetailsController::class);
        Route::get('getInventories', [SalesDetailsController::class, 'getInventories'])->name('getInventories');
        Route::post('sales_detail/add', [SalesDetailsController::class, 'addProduct'])->name('sales_details.add');
        Route::get('passDataSalesDetail', [SalessController::class, 'passDataSalesDetail'])->name('pass-data-sales-details');
        Route::get('passDataInventory', [SalessController::class, 'passDataInventory'])->name('pass-data-inventory');
        Route::get('form/dodolan', [SalessController::class, 'formDodolan'])->name('form-dodolan');
        Route::post('saless/store', [SalessController::class, 'store'])->name('sales-store');
        Route::get('saless/create', [SalessController::class, 'create'])->name('sales-create');
        Route::post('sales-details/store', [SalessController::class, 'storeSalesDetails'])->name('sales-details-store');
        Route::get('sales/get-sales-id', [SalessController::class, 'getSalesId'])->name('get-sales-id');
        Route::put('update-sales-details/{salesId}', [SalessController::class, 'update'])->name('update-sales-details');
        Route::get('edit-sales/{sales_id}', [SalessController::class, 'editSalesDetails'])->name('edit-sales');
        Route::get('getSalesTotalPrice/{salesId}', [SalessController::class, 'getTotalSalesPrice'])->name('getSalesTotalPrice');
        Route::get('get-stock/{id}', [SalessController::class, 'getStock'])->name('get-stock');
        Route::delete('delete-sales/{id}', [SalessController::class, 'deleteSales'])->name('deleteSales');
        Route::get('sales/export/xlsx', [SalessController::class, 'exportExcel'])->name('sales.export.excel');
        Route::get('sales/export/csv', [SalessController::class, 'exportCSV'])->name('sales.export.csv');
        Route::get('sales/export/pdf', [SalessController::class, 'exportPDF'])->name('sales.export.pdf');
    });

    Route::group(['middleware' => 'role:superadmin,purchase,manager'], function() {
        Route::get('inventory-data', [PurchaseController::class, 'getInventory'])->name('get-inventory');
        Route::get('get-purchase', [PurchaseController::class, 'index'])->name('get.purchase.index');
        Route::get('purchase_form', [PurchaseController::class, 'getPurchaseData'])->name('purchase_form');
        Route::post('purchase_store', [PurchaseController::class, 'store'])->name('purchase-store');
        Route::post('purchase_store_tok', [PurchaseController::class, 'storePurchase'])->name('purchase-store-tok');
        // Route::put('purchase-edit', [PurchaseController::class, 'updatePurchase'])->name('update-purchase');
        Route::delete('purchase-delete', [PurchaseController::class, 'deletePurchase'])->name('delete-purchase');
        Route::get('edit-purchase/{purchase_id}', [PurchaseController::class, 'edit_purchase'])->name('edit-purchase');
        Route::get('get-purchase/{purchaseId}', [PurchaseController::class, 'get_purchase'])->name('get-purchase');
        Route::put('update-purchase/{purchaseId}', [PurchaseController::class, 'update'])->name('update-purchase');
        Route::delete('delete-purchase/{id}', [PurchaseController::class, 'deletePurchase'])->name('deletePurchase');
        Route::get('purchase/export/xlsx', [PurchaseController::class, 'exportExcel'])->name('purchase.export.excel');
        Route::get('purchase/export/csv', [PurchaseController::class, 'exportCSV'])->name('purchase.export.csv');
        Route::get('purchase/export/pdf', [PurchaseController::class, 'exportPDF'])->name('purchase.export.pdf');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
