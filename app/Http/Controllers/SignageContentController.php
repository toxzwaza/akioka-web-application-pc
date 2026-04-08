<?php

namespace App\Http\Controllers;

use App\Models\CraneInspectionColor;
use App\Models\Safety;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

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

    public function delivery(){
        return Inertia::render('Signage/Content/Delivery');
    }

    public function schedule(){
        // 全てのファイルを取得
        $files = Storage::files('public/schedule_file');
        // 最新のタイムスタンプのファイルを取得
        $latestFile = collect($files)
            ->filter(function ($file) {
                return Str::endsWith($file, '.png');
            })
            ->sort()
            ->last();

        $latestFileName = basename($latestFile);


        return Inertia::render('Signage/Content/Schedule', ['file_path' => $latestFileName ]);
    }
}
