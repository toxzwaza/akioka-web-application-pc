<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\SignageController;
use App\Models\Signage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/getStorageAddresses', [ApiController::class, 'getStorageAddresses'])->name('api.getStorageAddresses');

