<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CalcProductController;
use App\Http\Controllers\CalcProductTabletController;
use App\Http\Controllers\FaxController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LunchController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MovieCategoryAndTagController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NewMovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RaspiController;
use App\Http\Controllers\SignageContentController;
use App\Http\Controllers\SignageController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockTabletController;
use App\Http\Controllers\TestController;
use App\Models\RaspiData;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemperatureAndHumidity;

Route::get('/test', [TestController::class, 'test'])->name('test');
Route::get('/storage_address/test', [TestController::class, 'storage_address_test'])->name('storage_address.test');
Route::get('/suppliers/test', [TestController::class, 'supplier_test'])->name('supplier.test');


Route::get('login', [MainController::class, 'login'])->name('login');
Route::get('login_store', [MainController::class, 'login_store'])->name('login.store');
Route::get('logout', [MainController::class, 'logout'])->name('logout');

// ホーム
Route::get('/', [MainController::class, 'index'])->name('home');

// 製品棚卸し
Route::get('/calc/product/test', [CalcProductController::class, 'test'])->name('calc.product.test');

Route::get('/calc/product', [CalcProductController::class, 'index'])->name('calc.product');
Route::post('/calc/product/store', [CalcProductController::class, 'store'])->name('calc.product.store');
Route::get('/calc/product/start', [CalcProductController::class, 'start'])->name('calc.product.start');

//// タブレット画面
// Route::get('/calc/product/tablet', [CalcProductTabletController::class, 'index'])->name('/calc/product/tablet');



Route::get('/order', [OrderController::class, 'index'])->name('order');
// 消耗品発注依頼リスト
Route::get('/order/consumOrders', [OrderController::class, 'consumOrders'])->name('order.consumOrders');
// 消耗品発注編集
Route::post('/order/consumOrders/store',[OrderController::class, 'store_consumOrders'])->name('order.store.consumOrders');

// 消耗品発注完了
Route::get('/order/consumOrders/complete', [OrderController::class, 'complete_consumOrders'])->name('order.complete.consumOrders');
// 消耗品発注依頼削除
Route::get('/order/consumOrders/delete', [OrderController::class, 'delete_consumOrders'])->name('order.delete.consumOrders');

Route::get('/order/consumOrders/{consumOrder_id}', [OrderController::class, 'print_consumOrders'])->name('order.print.consumOrders');



Route::get('/order/already/orders', [OrderController::class, 'already_orders'])->name('order.already_orders');
Route::get('/order/orders/create', [OrderController::class, 'create_orders'])->name('order.orders.create');
Route::get('/order/already_requests', [OrderController::class, 'already_requests'])->name('order.already_requests');
Route::get('/order/approval/{id}', [OrderController::class, 'approval_judge'])->name('order.approval.judge');
Route::get('/order/object_request/{id}', [OrderController::class, 'object_request_judge'])->name('order.object_request.judge');

Route::get('/lunch', [LunchController::class, 'index'])->name('lunch');
Route::get('/lunch/order-archive', [LunchController::class, 'order_archive'])->name('lunch.order-archive');
Route::get('/lunch/order-users', [LunchController::class, 'order_users'])->name('lunch.order-users');
Route::get('/lunch/create-description', [LunchController::class, 'create_description'])->name('lunch.create_description');
Route::post('/lunch/store-description', [LunchController::class, 'store_description'])->name('lunch.store_description');
Route::get('/getMonthOrders', [LunchController::class, 'getMonthOrders']);




// ファイル管理システム
Route::get('file', [FileController::class, 'index'])->name('file');


// サイネージ
// テスト用
Route::get('/signage/test', [SignageController::class, 'test'])->name('signage.test');

// サイネージ用PDFの追加・一覧表示
Route::get('/signage', [SignageController::class, 'index'])->name('signage.home');
Route::post('/signage/store', [SignageController::class, 'store'])->name('signage.store');
Route::get('/signage/show/{id}', [SignageController::class, 'show'])->name('signage.show');

// サイネージコンテンツ
// --連続安全日数--
Route::get('/signage/content/safety', [SignageContentController::class, 'safety'])->name('signage.content.safety');
// --点検色--
Route::get('/signage/content/inspectionCraneColor', [SignageContentController::class, 'inspectionCraneColor'])->name('signage.content.inspectionCraneColor');
// --納品状況--
Route::get('/signage/content/stockDeliveryList', [SignageContentController::class, 'stockDeliveryList'])->name('signage.content.stockDeliveryList');
Route::get('/stock/tablet/getDeliveryOrders', [StockTabletController::class, 'getDeliveryOrders'])->name('stock.tablet.getDeliveryOrders');
// 役職者のスケジュール表示
Route::get('/signage/content/schedule', [SignageContentController::class, 'schedule']);

// 全てのデータ取得
Route::get('/signage/getData', [SignageController::class, 'getData'])->name('signage.getData');
// データ削除
Route::get('/signage/deleteData/{asset_id}', [SignageController::class, "deleteData"])->name('signage.deleteData');
// データ更新
Route::get('/signage/updateData', [SignageController::class, 'updateData'])->name('signage.updateData');

// API
Route::get('/api/getAddress', [ApiController::class, 'getAddress']);
// Route::get('/api/getMovieTags', [ApiController::class, 'getMovieTags']);
Route::get('/api/getSuppliers',[ApiController::class, 'getSuppliers']);
Route::get('/api/respi', [RaspiController::class, 'raspi_data_store'])->name('raspi.data.store');

// 現場温度
Route::get('/temperatureAndHumidity', [TemperatureAndHumidity::class, "temperatureAndHumidity"])->name('api.temperatureAndHumidity');
Route::get('/getData', [TemperatureAndHumidity::class, "getData"])->name('api.getData');

// 温度書き出し用
Route::get('/export/RaspiData', [TemperatureAndHumidity::class, 'export_data'])->name('raspi.export.data');