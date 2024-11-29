<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StockTabletController extends Controller
{
    //
    public function index()
    {

        return Inertia::render('Stock/Tablet/Receive');
    }
}
