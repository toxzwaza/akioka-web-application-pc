<?php

namespace App\Http\Controllers;

use App\Models\CraneInspectionColor;
use App\Models\Safety;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SignageContentController extends Controller
{
    //
    public function safety(){
        $safety_date = Safety::orderby('created_at', 'desc')->first()->created_at;
        $current_date = now();
        $elapsed_days = $current_date->diffInDays($safety_date);


        return Inertia::render('Signage/Content/Safety', ['elapsed_days' => $elapsed_days]);
    }

    public function inspectionCraneColor(){
        $color = CraneInspectionColor::where('active_flg', '=', 1)->first();

        return Inertia::render('Signage/Content/InspectionCraneColor', ['color' => $color ]);
    }

    public function stockDeliveryList(){

        return Inertia::render('Signage/Content/StockDeliveryList');
    }
}
