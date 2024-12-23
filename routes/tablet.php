<?php
use App\Http\Controllers\StockTabletController;
use Illuminate\Support\Facades\Route;


Route::get('/stock/test', [StockTabletController::class, 'test']);
// 納品タブレット画面
Route::get('/stock/receive', [StockTabletController::class, 'index'])->name('stock.tablet.receive');
Route::get('/stock/getInitialOrders', [StockTabletController::class, 'getInitialOrders'])->name('stock.tablet.getInitialOrders');
// 納品書履歴確認画面(再登録や納品確定登録)
Route::get('/stock/archive', [StockTabletController::class, 'archive'])->name('stock.tablet.archive');
Route::get('/stock/getAlreadDelifileInitialOrders', [StockTabletController::class, 'getAlreadDelifileInitialOrders'])->name('stock.tablet.getAlreadDelifileInitialOrders');
// 納品登録画面
Route::get('/stock/delivery/{id}', [StockTabletController::class, 'delivery'])->name('stock.tablet.delivery');

// 注文データ削除
Route::get('/stock/delete/{order_id}', [StockTabletController::class, 'deleteInitialOrder'])->name('stock.tablet.delete.initialOrder');

// 在庫新規登録
Route::post('/stock/store', [StockTabletController::class, 'store'])->name('stock.tablet.store');
// 在庫登録せずにサイネージ表示
Route::get('/stock/none_storage/{order_id}', [StockTabletController::class, 'none_storage'])->name('stock.tablet.none_storage');
// 納品数量登録
Route::get('/stock/updateDelivery', [StockTabletController::class, 'updateDelivery'])->name('stock.tablet.updateDelivery');    


// 納品書アップロード
Route::post('/stock/uploadFile', [StockTabletController::class, 'uploadFile'])->name('stock.tablet.uploadFile');

// 納品受領登録
Route::get('/stock/receipt', [StockTabletController::class, 'receipt'])->name('stock.tablet.receipt');
Route::get('/stock/getAReceiptOrders', [StockTabletController::class, 'getReceiptOrders'])->name('stock.tablet.getReceiptOrders');
Route::get('/stock/updateReceipt/{id}', [StockTabletController::class, 'updateReceipt'])->name('stock.tablet.updateReceipt');  


// カテゴリーリスト取得
Route::get('/stock/getClassifications', [StockTabletController::class, 'getClassifications'])->name('stock.tablet.getClassifications');

// 仕入れ先リスト取得
Route::get('/stock/getSuppliers', [StockTabletController::class, 'getSuppliers'])->name('stock.tablet.getSuppliers');
