<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\ImportProduct;
use App\Models\InitialOrder;
use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\LastTreatRecord;
use App\Models\Log;
use App\Models\Movie;
use App\Models\MovieMemo;
use App\Models\Stock;
use App\Models\StockPriceArchive;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\StorageAddress;
use App\Models\Supplier;
use App\Models\User;
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



        $stock_storages = StockStorage::join('storage_addresses', 'storage_addresses.id', 'stock_storages.storage_address_id')->join('stocks', 'stocks.id', 'stock_storages.stock_id')->where('stocks.del_flg', 0)->whereIn('storage_addresses.location_id', [1,2])->get();
        dd(count($stock_storages));
        return;
        // // public/address_data.jsonを取得して配列で繰り返し表示
        // $jsonPath = public_path('address_data.json');
        
        // if (File::exists($jsonPath)) {
        //     $jsonContent = File::get($jsonPath);
        //     $addressData = json_decode($jsonContent, true);
            
        //     if ($addressData) {
        //         foreach ($addressData as $address) {
        //             echo "ID: " . $address['id'] . " - UID: " . $address['uid'];
        //             echo "<br>";
        //             $storage_address = StorageAddress::find($address['id']);
        //             $storage_address->uid = $address['uid'];
        //             $storage_address->save();
        //             echo "保存完了";
        //             echo "<br>";
        //         }
        //     } else {
        //         echo "JSONデータの解析に失敗しました";
        //         echo "<br>";
        //     }
        // } else {
        //     echo "address_data.jsonファイルが見つかりません";
        //     echo "<br>";
        // }


        return;
        //    サンプル承認フロー作成
        // $stock_storage_data = StockStorage::select('stock_id', 'stocks.name', 'stocks.s_name', 'stocks.img_path', 'stock_storages.id as stock_storage_id', 'quantity', 'storage_address_id')
        //     ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
        //     ->orderBy('storage_address_id', 'asc')
        //     ->get()
        //     ->groupBy('storage_address_id')
        //     ->mapWithKeys(function ($items, $key) {
        //         return [$key => array_map(function ($item) {
        //             if (strpos($item['img_path'], 'http') === false) {
        //                 $item['img_path'] = 'https://akioka.cloud/' . $item['img_path'];
        //             }
        //             return $item;
        //         }, $items->toArray())];
        //     })
        //     ->where('stocks.del_flg', 0)
        //     ->toArray();


        // return response()->json($stock_storage_data);

        $approval_flows = Helper::createApprovalFlow(
            39200,
            84,
            0
        );

        if (!empty($approval_flows)) {
            foreach ($approval_flows as $approval_flow) {
                $user = User::where('id', $approval_flow)->first();
                if ($user) {
                    echo $user->name;
                    echo "<br>";
                } else {
                    echo "ユーザーが見つかりません";
                    echo "<br>";
                }
            }
        } else {
            echo "承認フローが作成されていません";
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
