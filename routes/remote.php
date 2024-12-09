<?php
use App\Http\Controllers\RemoteController;
use Illuminate\Support\Facades\Route;


Route::get('/', [RemoteController::class, 'index'])->name('remote');
Route::get('/create', [ RemoteController::class, 'create'])->name('remote.create');
Route::post('/store', [RemoteController::class , 'store'])->name('remote.store');
