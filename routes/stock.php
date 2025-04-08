<?php

use App\Http\Controllers\AcceptController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\OrderRequestController;
use App\Http\Controllers\PurchaseOrder;
use App\Http\Controllers\RaspiController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockSupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemperatureAndHumidity;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\InitialOrderController;
use App\Http\Controllers\SupplierController;

// 在庫管理システム
Route::get('/test', [StockController::class, 'test'])->name('stock.test');
Route::get('/', [StockController::class, 'index'])->name('stock');
Route::get('/stocks', [StockController::class, 'stocks'])->name('stock.stocks');
Route::get('/stocks/get', [StockController::class, 'getStocks'])->name('stock.getStocks');
Route::get('/stocks/show/{stock_id}', [StockController::class, 'stock_show'])->name('stock.show.stocks');
Route::get('/stocks/stock-request/{stock_id}', [StockController::class, 'toggle_stock_request'])->name('stock.toggle.stock_request');
Route::post('/stocks/stock-request/update', [StockController::class, 'update_stock_request'])->name('stock.update.stock_request');

Route::post('/stocks/store', [StockController::class, 'store_stocks'])->name('stock.store.stocks');
// 新規在庫作成
Route::get('/stocks/create', [StockController::class, 'create_stocks'])->name('stock.stocks.create');
// Route::post('/stocks/store', [StockController::class, 'store_stocks'])->name('stock.stocks.store');

Route::get('/stocks/taking', [StockController::class, 'stock_taking'])->name('stock.stocks.taking');
Route::get('/stocks/inventory/records', [StockController::class, 'getInventoryOperationRecords'])->name('stock.stocks.getInventoryOperationRecords');
// 対象の日付の入出庫データを取得
Route::get('/stocks/inventory/records/date', [StockController::class, 'getInventoryOperationRecordsByDate'])->name('stock.stocks.getInventoryOperationRecordsByDate');

// 発注関連
Route::get('/initial-order', [InitialOrderController::class, 'index'])->name('stock.initialOrders');
Route::get('/initial-order/create', [InitialOrderController::class, 'create'])->name('stock.create.initialOrders');
// 発注登録
Route::post('/initial-order/store', [InitialOrderController::class, 'store'])->name('stock.store.initialOrders');
// 納入希望日設定
Route::post('/initial-order/update/desired_delivery-date', [InitialOrderController::class, 'update_desired_delivery_date'])->name('stock.update_desired_delivery_date');


// 発注修正
Route::get('/initial-orders/edit', [StockController::class, 'initial_orders'])->name('stock.initial_orders');
Route::post('/initial-orders/update', [StockController::class, 'update_initial_order'])->name('stock.update_initial_order');
Route::post('/initial-orders/update/expected-delivery-date', [StockController::class, 'update_expected_delivery_date'])->name('stock.update_expected_delivery_date');
Route::post('/initial-orders/update/delivery-date', [StockController::class, 'update_delivery_date'])->name('stock.update_delivery_date');

// 発注依頼一覧
Route::get('/order-requests', [OrderRequestController::class, 'index'])->name('stock.order_requests');
// 発注書表示
Route::get('/purchase-order/{order_request_id}', [PurchaseOrder::class, 'index'])->name('stock.purchase_order');
// 発注依頼取得
Route::get('/order-requests/get', [OrderRequestController::class, 'getOrderRequests'])->name('stock.getOrderRequests');
// 承認依頼
Route::post('/accept/order-request', [AcceptController::class, 'sendAccept'])->name('stock.accept.order_request');
// 承認用画面
Route::get('/accept', [AcceptController::class, 'index'])->name('stock.accept');
// 承認結果送信
Route::post('/accept/store', [AcceptController::class, 'store'])->name('stock.accept.store');
// 発注完了
Route::put('/order-requests/complete', [OrderRequestController::class, 'delete'])->name('stock.completeOrderRequest');
// 発注依頼削除
Route::delete('/order-requests/delete', [OrderRequestController::class, 'delete'])->name('stock.deleteOrderRequest');
// 発注依頼から発注作成
Route::post('/order-requests/create/initial-order', [OrderRequestController::class, 'createInitialOrder'])->name('stock.createInitialOrder');

// 滞留品
Route::get('/retentions', [RetentionController::class, 'index'])->name('stock.retentions');
Route::get('/retentions/get', [RetentionController::class, 'getRetentionStocks'])->name('stock.getRetentionStocks');

// 旧
Route::get('/retained', [StockController::class, 'retained_stocks'])->name('stock.retained.stocks');
Route::post('/retained/store', [StockController::class, 'store_retained_stocks'])->name('stock.store.retained.stocks');
Route::post('/retained/last/store', [StockController::class, 'store_last_treat_record'])->name('stock.store.last_retained.stocks');

// 得意先
Route::post('/stock-suppliers/store', [StockSupplierController::class, 'store'])->name('stock.stock_supplier.store');
// Route::get('/suppliers/add', [StockController::class, 'stock_add_supplier'])->name('stock.stocks.add_supplier');
// Route::post('/suppliers/store', [StockController::class, 'store_stock_suppliers'])->name('stock.store.stock_suppliers');
// Route::get('/suppliers/delete', [StockController::class, 'delete_stock_suppliers'])->name('stock.delete.stock_suppliers');

// 格納先
Route::get('/storage-addresses', [StockController::class, 'storage_address'])->name('stock.storage_addresses');
Route::get('/storage-addresses/create', [StockController::class, 'create_storage_addresses'])->name('stock.storage_addresses.create');
Route::post('/storage-addresses/store', [StockController::class, 'store_storage_address'])->name('stock.storage_address.store');
Route::get('/storage-addresses/print', [StockController::class, 'print'])->name('stock.storage_addresses.print');

// 監視カメラ録画映像
Route::get('/camera', [CameraController::class, 'index'])->name('stock.camera');
Route::get('/camera/movies', [CameraController::class, 'getCameraMovies'])->name('stock.getCameraMovies');

Route::post('/stock-storage/update', [StockController::class, 'update_stock_storage'])->name('stock.stock_storage.update');
Route::get('/stock-storage/delete', [StockController::class, 'delete_stock_storage'])->name('stock.stock_storage.delete');
Route::post('/stock-storage/create', [StockController::class, 'create_stock_storage'])->name('stock.stock_storage.create');

Route::get('/suppliers', [SupplierController::class, 'index'])->name('stock.suppliers');
Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('stock.suppliers.create');
Route::post('/suppliers/store', [SupplierController::class, 'store'])->name('stock.suppliers.store');
Route::get('/suppliers/edit/{supplier_id}', [SupplierController::class, 'edit'])->name('stock.suppliers.edit');

// Location追加
Route::get('/locations/create', [StockController::class, 'store_location'])->name('stock.locations.create');