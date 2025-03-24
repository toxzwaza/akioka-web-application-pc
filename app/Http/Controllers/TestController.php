<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\InitialOrder;
use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\LastTreatRecord;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\StorageAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TestController extends Controller
{
    //
    public function test()
    {
        $url = "http://192.168.0.143:5000/movie/youtube_upload?id=244&file_path=%5C%5Cfserver%5Cshare%5CRPA%5Cbentou%5Ctesat.mp4&title=test&description=test";

        // Promiseを待機
        $promise = Http::async()->get($url);
        
        try {
            $response = $promise->wait();
            if ($response->successful()) {
                // 処理を実行しながらreturn で trueをjsonで変換
                return response()->json(true);
            } else {
                return response()->json(false);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'エラーが発生しました: ' . $e->getMessage()]);
        }

    

    }

    public function storage_address_test()
    {
        $storage_addresses = StorageAddress::all();

        foreach ($storage_addresses as $storage_address) {
            list($alphabet, $number1, $number2) = explode('-', $storage_address->address);
            // dd($alphabet,$number1,$number2);
            $storage_address->shelf = $alphabet;
            $storage_address->row = $number1;
            $storage_address->col = $number2;
            $storage_address->save();
        }
    }

    public function supplier_test()
    {
        $stock_id = 5361;
        $stock_suppliers = StockSupplier::select('suppliers.*', 'stock_suppliers.lead_time', 'stock_suppliers.act_flg')->where('stock_id', $stock_id)->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->get();
        dd($stock_suppliers);
    }
}
