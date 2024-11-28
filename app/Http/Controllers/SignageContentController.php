<?php

namespace App\Http\Controllers;

use App\Models\Safety;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SignageContentController extends Controller
{
    //
    public function index(){
        $safety_date = Safety::orderby('created_at', 'desc')->first()->created_at;
        $current_date = now();
        $elapsed_days = $current_date->diffInDays($safety_date);


        return Inertia::render('Signage/Content/Safety', ['elapsed_days' => $elapsed_days]);
    }
}
