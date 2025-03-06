<?php

use App\Http\Controllers\MasterController;

use Illuminate\Support\Facades\Route;

// 基幹マスタ管理
Route::get('/', [MasterController::class, 'index'])->name('master');
Route::get('/users', [MasterController::class, 'users'])->name('master.users');
Route::get('/akioka-users', [MasterController::class, 'akioka_users'])->name('master.akioka-users');
Route::get('/calender', [MasterController::class, 'calender'])->name('master.calender');
Route::get('/get/holidays', [MasterController::class, 'get_holidays'])->name('master.get.holidays');
Route::post('/store/holiday', [MasterController::class, 'store_holiday'])->name('master.store.holiday');

Route::get('/create/users', [MasterController::class, 'create_user'])->name('master.create.user');
Route::post('/store/users', [MasterController::class, 'store_user'])->name('master.store.users');
Route::get('/store', [MasterController::class, 'store'])->name('master.store');
Route::get('/edit/users/{user_id}', [MasterController::class, 'edit_user'])->name('master.edit.user');
