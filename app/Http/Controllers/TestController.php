<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    //
    public function test(){
        $path = env('AKIOKA_APP_STOCK_PATH');
        // dd($path);
        if(File::exists($path . '\stock\new_file_5149.jpg')){
            echo "ファイルが存在します。";
        }else{
            echo "ファイルが存在しません。";
        }

    }
}
