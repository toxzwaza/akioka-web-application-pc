<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\OrderRequestController;
use App\Http\Controllers\RaspiController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockSupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemperatureAndHumidity;

// 在庫管理システム
Route::get('/test', [StockController::class, 'test'])->name('stock.test');
Route::get('/', [StockController::class, 'index'])->name('stock');
Route::get('/stocks', [StockController::class, 'stocks'])->name('stock.stocks');
Route::get('/edit/stocks/{stock_id}', [StockController::class, 'stock_edit'])->name('stock.edit.stocks');
Route::post('/store/stocks', [StockController::class, 'store_stocks'])->name('stock.store.stocks');
Route::get('/stocks/create', [StockController::class, 'create_stocks'])->name('stock.stocks.create');
Route::get('/stocks/taking', [StockController::class, 'stock_taking'])->name('stock.stocks.taking');
Route::get('/stocks/getInventoryOperationRecords', [StockController::class, 'getInventoryOperationRecords'])->name('stock.stocks.getInventoryOperationRecords');
// 対象の日付の入出庫データを取得
Route::get('/stocks/getInventoryOperationRecordsByDate', [StockController::class, 'getInventoryOperationRecordsByDate'])->name('stock.stocks.getInventoryOperationRecordsByDate');

// 発注登録
Route::get('/initialOrder/stocks/{stock_id}', [StockController::class, 'order_stock'])->name('stock.order');
Route::post('/initialOrder/store', [StockController::class, 'order_store'])->name('stock.order.store');
// 全ての発注データを取得
Route::get('/getAllInitialOrders', [StockController::class, 'getAllInitialOrders'])->name('stock.getAllInitialOrders');

// 発注修正
Route::get('stocks/initialOrders', [StockController::class, 'initial_orders'])->name('stock.initial_orders');
Route::post('stocks/update/initial_order', [StockController::class, 'update_initial_order'])->name('stock.update_initial_order');
Route::post('stocks/update/expected_delivery_date', [StockController::class, 'update_expected_delivery_date'])->name('stock.update_expected_delivery_date');

// 発注依頼一覧
Route::get('stocks/order_requests', [OrderRequestController::class, 'index'])->name('stock.order_requests');
// 発注依頼取得
Route::get('stocks/getOrderRequests', [OrderRequestController::class, 'getOrderRequests'])->name('stock.getOrderRequests');
// 発注完了
Route::put('stocks/completeOrderRequest', [OrderRequestController::class, 'completeOrderRequest'])->name('stock.completeOrderRequest');



////////// 滞留品 //////////
Route::get('/retentions/stocks', [RetentionController::class, 'index'])->name('stock.retentions');
Route::get('/getRetentionStocks', [RetentionController::class, 'getRetentionStocks'])->name('stock.getRetentionStocks');

// 旧
Route::get('/retained/stocks', [StockController::class, 'retained_stocks'])->name('stock.retained.stocks');
Route::post('/reatained/store', [StockController::class, 'store_retained_stocks'])->name('stock.store.retained.stocks');
Route::post('/last_reatained/store', [StockController::class, 'store_last_treat_record'])->name('stock.store.last_retained.stocks');



Route::get('/stocks/add_supplier', [StockController::class, 'stock_add_supplier'])->name('stock.stocks.add_supplier');
Route::post('/stock_suppliers/store', [StockController::class, 'store_stock_suppliers'])->name('stock.store.stock_suppliers');
Route::get('/stock_suppliers/delete', [StockController::class, 'delete_stock_suppliers'])->name('stock.delete.stock_suppliers');


// 格納先
Route::get('/storage_addresses', [StockController::class, 'storage_address'])->name('stock.storage_addresses');
Route::get('/storage_addresses/create', [StockController::class, 'create_storage_addresses'])->name('stock.storage_addresses.create');
Route::get('/create/storage_addresses', [StockController::class, 'store_storage_address'])->name('stock.storage_address.create');
Route::get('/storage_addresses/print', [StockController::class, 'print'])->name('stock.storage_addresses.print');




Route::post('/stock_storage/update', [StockController::class, 'update_stock_storage'])->name('stock.stock_storage.update');
Route::get('/stock_storage/delete', [StockController::class, 'delete_stock_storage'])->name('stock.stock_storage.delete');
Route::post('/stock_storage/create', [StockController::class, 'create_stock_storage'])->name('stock.stock_storage.create');

Route::get('/suppliers', [StockSupplierController::class, 'index'])->name('stock.suppliers');
Route::get('/suppliers/create', [StockSupplierController::class, 'create'])->name('stock.suppliers.create');
Route::post('/suppliers/store', [StockSupplierController::class, 'store'])->name('stock.suppliers.store');
Route::get('/suppliers/edit/{supplier_id}', [StockSupplierController::class, 'edit'])->name('stock.suppliers.edit');


// Location追加
Route::get('/create/locations', [StockController::class, 'store_location'])->name('stock.locations.create');