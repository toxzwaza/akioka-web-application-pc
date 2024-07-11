<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\LunchController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
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
Route::get('/stock/stocks/create', [StockController::class, 'create_stocks'])->name('stock.stocks.create');

Route::get('/stock/stock_storages', [StockController::class, 'stock_storages'])->name('stock.stock_storages');
Route::get('/stock/stock_storages/create', [StockController::class, 'create_stock_storages'])->name('stock.stock_storages.create');

Route::get('/stock/suppliers', [StockController::class, 'suppliers'])->name('stock.suppliers');
Route::get('/stock/suppliers/create', [StockController::class, 'create_suppliers'])->name('stock.suppliers.create');
Route::get('/stock/edit/suppliers/{supplier_id}', [StockController::class, 'supplier_edit'])->name('stock.suppliers.edit');
// Location追加
Route::get('/stock/create/locations', [StockController::class, 'store_location'])->name('stock.locations.create');
// StorageAddress作成
Route::get('stock/create/storage_addresses', [StockController::class, 'store_storage_address'])->name('stock.storage_address.create');


Route::get('/order', [OrderController::class, 'index'])->name('order');
Route::get('/lunch', [LunchController::class, 'index'])->name('lunch');



// API
Route::get('/api/getAddress', [ApiController::class, 'getAddress']);