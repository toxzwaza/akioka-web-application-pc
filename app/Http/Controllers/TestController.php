<?php

namespace App\Http\Controllers;

use App\Models\InventoryOperationRecord;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\StorageAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    //
    public function test(){


        // $path = env('AKIOKA_APP_STOCK_PATH');
        // // dd($path);
        // if(File::exists($path . '\stock\new_file_5149.jpg')){
        //     echo "ファイルが存在します。";
        // }else{
        //     echo "ファイルが存在しません。";
        // }

    }

    public function storage_address_test(){
        $storage_addresses = StorageAddress::all();

        foreach($storage_addresses as $storage_address){
            list($alphabet, $number1, $number2) = explode('-', $storage_address->address);
            // dd($alphabet,$number1,$number2);
            $storage_address->shelf = $alphabet;
            $storage_address->row = $number1;
            $storage_address->col = $number2;
            $storage_address->save();
        }
    }

    public function supplier_test(){
        $stock_id = 5361;
        $stock_suppliers = StockSupplier::select('suppliers.*','stock_suppliers.lead_time','stock_suppliers.act_flg')->where('stock_id',$stock_id)->join('suppliers','suppliers.id','stock_suppliers.supplier_id')->get();
        dd($stock_suppliers);
    }
}
