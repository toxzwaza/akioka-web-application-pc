<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\LunchController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/test', [TestController::class, 'test'])->name('test');
Route::get('/storage_address/test', [TestController::class, 'storage_address_test'])->name('storage_address.test');
Route::get('/suppliers/test', [TestController::class, 'supplier_test'])->name('supplier.test');


Route::get('login', [MainController::class, 'login'])->name('login');
Route::get('login_store', [MainController::class, 'login_store'])->name('login.store');
Route::get('logout', [MainController::class, 'logout'])->name('logout');

// ホーム
Route::get('/', [MainController::class, 'index'])->name('home');

// 基幹マスタ管理
Route::get('/master', [MasterController::class, 'index'])->name('master');
Route::get('/master/users', [MasterController::class, 'users'])->name('master.users');
Route::get('/master/akioka-users', [MasterController::class, 'akioka_users'])->name('master.akioka-users');
Route::get('/master/create/users', [MasterController::class, 'create_user'])->name('master.create.user');
Route::get('/master/store', [MasterController::class, 'store'])->name('master.store');
Route::get('/master/edit/users/{user_id}', [MasterController::class, 'edit_user'])->name('master.edit.user');

// 在庫管理システム
Route::get('/stock', [StockController::class, 'index'])->name('stock');
Route::get('/stock/stocks', [StockController::class, 'stocks'])->name('stock.stocks');
Route::get('/stock/edit/stocks/{stock_id}', [StockController::class, 'stock_edit'])->name('stock.edit.stocks');
Route::post('/stock/store/stocks', [StockController::class, 'store_stocks'])->name('stock.store.stocks');
Route::get('/stock/stocks/create', [StockController::class, 'create_stocks'])->name('stock.stocks.create');


Route::get('/stock/storage_addresses', [StockController::class, 'storage_address'])->name('stock.storage_addresses');
Route::get('/stock/storage_addresses/create', [StockController::class, 'create_storage_addresses'])->name('stock.storage_addresses.create');
Route::get('stock/create/storage_addresses', [StockController::class, 'store_storage_address'])->name('stock.storage_address.create');

Route::post('/stock/stock_storage/update', [StockController::class, 'update_stock_storage'])->name('stock.stock_storage.update');
Route::get('/stock/stock_storage/delete', [StockController::class, 'delete_stock_storage'])->name('stock.stock_storage.delete');

Route::get('/stock/suppliers', [StockController::class, 'suppliers'])->name('stock.suppliers');
Route::get('/stock/suppliers/create', [StockController::class, 'create_suppliers'])->name('stock.suppliers.create');
Route::get('/stock/edit/suppliers/{supplier_id}', [StockController::class, 'supplier_edit'])->name('stock.suppliers.edit');

// Location追加
Route::get('/stock/create/locations', [StockController::class, 'store_location'])->name('stock.locations.create');




Route::get('/order', [OrderController::class, 'index'])->name('order');
Route::get('/order/already/orders', [OrderController::class, 'already_orders'])->name('order.already_orders');
Route::get('/order/orders/create', [OrderController::class, 'create_orders'])->name('order.orders.create');
Route::get('/order/already_requests', [OrderController::class, 'already_requests'])->name('order.already_requests');
Route::get('/order/approval/{id}', [OrderController::class, 'approval_judge'])->name('order.approval.judge');
Route::get('/order/object_request/{id}', [OrderController::class, 'object_request_judge'])->name('order.object_request.judge');

Route::get('/lunch', [LunchController::class, 'index'])->name('lunch');

Route::get('/movie', [MovieController::class, 'index'])->name('movie');
Route::get('/movie/{movie_id}', [MovieController::class, 'show'])->name('movie.show');
Route::get('movie/memo/delete/{memo_id}', [MovieController::class, 'movie_memo_delete'])->name('movie.memo.delete');

Route::post('movie/memo/update', [MovieController::class, 'movie_memo_update'])->name('movie.memo.update');



// API
Route::get('/api/getAddress', [ApiController::class, 'getAddress']);
Route::get('/AddMemo', [MovieController::class, 'addMemo']);