<?php

use App\Http\Controllers\AcceptController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CallBackController;
use App\Http\Controllers\OrderRequestController;
use App\Http\Controllers\PurchaseOrder;
use App\Http\Controllers\RaspiController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockExportController;
use App\Http\Controllers\StockSupplierController;
use App\Http\Controllers\StockStorageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemperatureAndHumidity;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\InitialOrderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StockCountController;
use App\Http\Controllers\SupplierController;

// 在庫管理システム
Route::get('/test', [StockController::class, 'test'])->name('stock.test');
Route::get('/', [StockController::class, 'index'])->name('stock');
Route::get('/stocks', [StockController::class, 'stocks'])->name('stock.stocks');
Route::get('/stocks/get', [StockController::class, 'getStocks'])->name('stock.getStocks');
Route::get('/stocks/show/{stock_id}', [StockController::class, 'stock_show'])->name('stock.show.stocks');
// 発注履歴取得
Route::get('/stocks/initial-orders/get', [StockController::class, 'getInitialOrders'])->name('stock.getInitialOrders');
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
// 発注依頼
Route::delete('/initial-order/delete', [InitialOrderController::class, 'delete'])->name('stock.delete.initialOrders');
// 発注登録
Route::post('/initial-order/store', [InitialOrderController::class, 'store'])->name('stock.store.initialOrders');
// 納品書変更
Route::post('/initial-order/update/deli_file', [InitialOrderController::class, 'update_deli_file'])->name('stock.update_deli_file');
Route::post('/order-requests/store/approval_document', [OrderRequestController::class, 'storeApprovalDocument'])->name('stock.store.approval_document');
Route::post('/order-requests/send-device-message', [OrderRequestController::class, 'sendDeviceMessage'])->name('stock.sendDeviceMessage');
// 発注先再ロード
Route::post('/order-requests/reload/supplier', [OrderRequestController::class, 'reloadSupplier'])->name('stock.reloadSupplier');




// 発注修正
Route::get('/initial-orders/edit', [StockController::class, 'initial_orders'])->name('stock.initial_orders');
Route::post('/initial-orders/update', [StockController::class, 'update_initial_order'])->name('stock.update_initial_order');

Route::post('/initial-order/update/date', [InitialOrderController::class, 'update_date'])->name('stock.update_date');

// 単価変更に伴う再発注依頼
Route::post('/initial-order/update/price', [InitialOrderController::class, 'update_price'])->name('stock.update_price');

// 単価もしくは送料を変更
Route::post('/initial-order/update/data', [InitialOrderController::class, 'update_data'])->name('stock.update_data');
// 発注済み登録機能
Route::post('/initial-order/update/order_complete', [InitialOrderController::class, 'updateOrderComplete'])->name('stock.updateOrderComplete');
// デバイスメッセージ送信
Route::post('/initial-order/send-device-message', [InitialOrderController::class, 'sendDeviceMessage'])->name('stock.initialOrder.sendDeviceMessage');

// Route::post('/initial-order/update/desired_delivery-date', [InitialOrderController::class, 'update_desired_delivery_date'])->name('stock.update_desired_delivery_date');
// Route::post('/initial-orders/update/expected-delivery-date', [StockController::class, 'update_expected_delivery_date'])->name('stock.update_expected_delivery_date');
// Route::post('/initial-orders/update/delivery-date', [StockController::class, 'update_delivery_date'])->name('stock.update_delivery_date');


// 発注依頼一覧
Route::get('/order-requests', [OrderRequestController::class, 'index'])->name('stock.order_requests');
// 発注書表示
Route::get('/purchase-order/{order_request_id}', [PurchaseOrder::class, 'index'])->name('stock.purchase_order');
// 発注依頼取得
Route::get('/order-requests/get', [OrderRequestController::class, 'getOrderRequests'])->name('stock.getOrderRequests');
// 承認依頼
Route::post('/accept/order-request', [AcceptController::class, 'sendAccept'])->name('stock.accept.order_request');
// 見積確認中
Route::post('/accept/order-request/change-estimate', [AcceptController::class, 'changeEstimate'])->name('stock.accept.order_request.change-estimate');
// 承認スキップ
Route::post('/accept/order-request/skip', [AcceptController::class, 'skipAccept'])->name('stock.accept.order_request.skip');
// 承認用画面
Route::get('/accept', [AcceptController::class, 'index'])->name('stock.accept');
// 承認結果送信
Route::post('/accept/store', [AcceptController::class, 'store'])->name('stock.accept.store');
// 発注依頼更新（数量、単価、金額、送料）
Route::put('/order-requests/update', [OrderRequestController::class, 'updateOrderRequest'])->name('stock.updateOrderRequest');
// コールバック
Route::post('/callback', [CallBackController::class, 'callback'])->name('stock.callback');
// 発注担当者コメント更新
Route::post('/order-requests/update/sub_description', [OrderRequestController::class, 'updateSubDescription'])->name('stock.updateSubDescription');
// 未在庫登録品 ID紐づけ
Route::post('/order-requests/update/stock-id', [OrderRequestController::class, 'updateStockId'])->name('stock.updateStockId');
Route::post('/accept/order-request/re-notify', [AcceptController::class, 'reNotify'])->name('stock.accept.order_request.re-notify');
//非承認
Route::post('/accept/order-request/reject', [AcceptController::class, 'sendReject'])->name('stock.accept.order_request.reject');

// 発注完了
Route::put('/order-requests/complete', [OrderRequestController::class, 'delete'])->name('stock.completeOrderRequest');
// 発注依頼削除
Route::delete('/order-requests/delete', [OrderRequestController::class, 'delete'])->name('stock.deleteOrderRequest');
// 発注依頼から発注作成
Route::post('/order-requests/create/initial-order', [OrderRequestController::class, 'createInitialOrder'])->name('stock.createInitialOrder');

// デバイスID設定
Route::post('/order-requests/set-device-id', [OrderRequestController::class, 'setDeviceId'])->name('stock.setDeviceId');

// 滞留品
Route::get('/retentions', [RetentionController::class, 'index'])->name('stock.retentions');
Route::get('/retentions/get', [RetentionController::class, 'getRetentionStocks'])->name('stock.getRetentionStocks');

// 旧
Route::get('/retained', [StockController::class, 'retained_stocks'])->name('stock.retained.stocks');
Route::post('/retained/store', [StockController::class, 'store_retained_stocks'])->name('stock.store.retained.stocks');
Route::post('/retained/last/store', [StockController::class, 'store_last_treat_record'])->name('stock.store.last_retained.stocks');

// 得意先
Route::post('/stock-suppliers/store', [StockSupplierController::class, 'store'])->name('stock.stock_supplier.store');
Route::post('/stock-suppliers/update', [StockSupplierController::class, 'update'])->name('stock.stock_supplier.update');
Route::delete('/stock-suppliers/delete', [StockSupplierController::class, 'delete'])->name('stock.stock_supplier.delete');
Route::post('/stock-suppliers/change/main-flg', [StockSupplierController::class, 'changeMainFlg'])->name('stock.stock_supplier.change.main_flg');


// 格納先
Route::get('/locations', [LocationController::class, 'index'])->name('stock.locations');
Route::post('/locations/store', [LocationController::class, 'store'])->name('stock.locations.store');
Route::get('/locations/show/{location_id}', [LocationController::class, 'show'])->name('stock.locations.show');

Route::post('/storage-addresses/store', [StockStorageController::class, 'store'])->name('stock.storage_addresses.store');
Route::get('/storage-addresses/print', [StockController::class, 'print'])->name('stock.storage_addresses.print');

Route::post('/stock-storage/update', [StockStorageController::class, 'update'])->name('stock.stock_storage.update');
Route::delete('/stock-storage/delete', [StockStorageController::class, 'delete'])->name('stock.stock_storage.delete');
// Route::post('/stock-storage/create', [StockController::class, 'create_stock_storage'])->name('stock.stock_storage.create');

// Route::get('/suppliers/add', [StockController::class, 'stock_add_supplier'])->name('stock.stocks.add_supplier');
// Route::post('/suppliers/store', [StockController::class, 'store_stock_suppliers'])->name('stock.store.stock_suppliers');
// Route::get('/suppliers/delete', [StockController::class, 'delete_stock_suppliers'])->name('stock.delete.stock_suppliers');

// 格納先
// Route::get('/storage-addresses', [StockController::class, 'storage_address'])->name('stock.storage_addresses');
// Route::get('/storage-addresses/create', [StockController::class, 'create_storage_addresses'])->name('stock.storage_addresses.create');
// Route::post('/storage-addresses/store', [StockController::class, 'store_storage_address'])->name('stock.storage_address.store');


// 監視カメラ録画映像
Route::get('/camera', [CameraController::class, 'index'])->name('stock.camera');
Route::get('/camera/movies', [CameraController::class, 'getCameraMovies'])->name('stock.getCameraMovies');

Route::get('/suppliers', [SupplierController::class, 'index'])->name('stock.suppliers');
Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('stock.suppliers.create');
Route::post('/suppliers/store', [SupplierController::class, 'store'])->name('stock.suppliers.store');
Route::get('/suppliers/edit/{supplier_id}', [SupplierController::class, 'edit'])->name('stock.suppliers.edit');

// Location追加
Route::get('/locations/create', [StockController::class, 'store_location'])->name('stock.locations.create');

// 棚卸用
Route::get('/stock-count/export', [StockCountController::class, 'export_data'])->name('stock.stock_count.export');
Route::get('/storage-address/export', [StockCountController::class, 'export_storage_address_data'])->name('stock.storage_address.export');

// 在庫・サプライヤー情報エクスポート
Route::get('/stocks/export-with-suppliers', [StockExportController::class, 'exportStocksWithSuppliers'])->name('stock.export.with_suppliers');