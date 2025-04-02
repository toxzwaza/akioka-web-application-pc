<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\ImportProduct;
use App\Models\InitialOrder;
use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\LastTreatRecord;
use App\Models\Stock;
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
        // $del_stocks = Stock::where('del_flg', 1)->get();
        // foreach ($del_stocks as $del_stock) {
        //     $del_stock->delete();
        // }
        // return;

        // $distinct_stocks = Stock::select('name', 's_name')
        //     ->groupBy('name', 's_name')
        //     ->havingRaw('COUNT(*) > 1')
        //     ->get();

        // echo count($distinct_stocks);


        // foreach ($distinct_stocks as $distinct_stock) {
        //     echo '<p>' . $distinct_stock->name . ' : ' . $distinct_stock->s_name . '</p>';

        //     $stocks = Stock::where('name', $distinct_stock->name)
        //         ->where('s_name', $distinct_stock->s_name)
        //         ->orderBy('id', 'desc')
        //         ->get();

        //     $latest_stock = $stocks->shift(); // 一番新しいレコードを取得してリストから削除

        //     foreach ($stocks as $stock) {
        //         $stock->delete();
        //     }
        // }
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
