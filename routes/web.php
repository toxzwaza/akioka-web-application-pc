<?php

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

Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('/master', [MasterController::class, 'index'])->name('master');
Route::get('/master/users', [MasterController::class, 'users'])->name('master.users');
Route::get('/master/create/users', [MasterController::class, 'create_user'])->name('master.create.user');
Route::get('/master/stock', [MasterController::class, 'stock'])->name('master.stock');
Route::get('/stock', [StockController::class, 'index'])->name('stock');
Route::get('/order', [OrderController::class, 'index'])->name('order');
Route::get('/lunch', [LunchController::class, 'index'])->name('lunch');