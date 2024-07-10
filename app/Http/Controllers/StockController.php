<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StorageAddress;
use Illuminate\Http\Request;

class StockController extends Controller
{
    //
    public function index()
    {
        

        return view('stock.index');
    }
    public function stocks()
    {
        $stocks = Stock::orderby('updated_at','desc')->take(100)->get();

        return view('stock.stocks', compact('stocks'));
    }
    public function stock_storages()
    {
        $storage_location_addresses = StorageAddress::select('storage_addresses.id as storage_address_id', 'locations.name as location_name', 'storage_addresses.address', 'storage_addresses.created_at')->join('locations', 'locations.id', 'storage_addresses.location_id')->get();
        foreach ($storage_location_addresses as $sla) {
            $stock_storage_count = StockStorage::where('storage_address_id', $sla->storage_address_id)->count();
            $sla->count = $stock_storage_count;
        }
        // dd($storage_location_addresses);



        return view('stock.stock_storages', compact('storage_location_addresses'));
    }
}
