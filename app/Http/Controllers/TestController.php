<?php

namespace App\Http\Controllers;

use App\Models\InventoryOperation;
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

        // すべての出庫情報を重複なしで取得
        // $records = InventoryOperationRecord::where('inventory_operation_id', 2)->distinct('stock_id')->pluck('stock_id');

        // 備品倉庫のすべての物品
        // 備品倉庫内のアドレスを取得
        $addresses = StorageAddress::where('location_id', 2)->pluck('id');
        $stocks = StockStorage::whereIn('storage_address_id', $addresses)->get();
        dd($stocks[0]);
      
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
