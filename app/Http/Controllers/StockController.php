<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\Location;
use App\Models\Process;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StorageAddress;
use App\Models\Supplier;
use Illuminate\Http\Request;

class StockController extends Controller
{
    //
    public function index()
    {
        $operation_records = InventoryOperationRecord::select('stocks.name as stock_name', 'inventory_operations.name as operation_name', 'inventory_operations.id as operation_id', 'inventory_operation_records.created_at', 'users.name as user_name', 'inventory_operation_records.quantity', 'inventory_operation_records.est_quantity')->join('inventory_operations', 'inventory_operations.id', 'inventory_operation_records.inventory_operation_id')->join('stock_storages', 'stock_storages.id', 'inventory_operation_records.stock_storage_id')->join('stocks', 'stocks.id', 'inventory_operation_records.stock_id')->join('users', 'users.id', 'inventory_operation_records.user_id')->orderby('inventory_operation_records.updated_at', 'desc')->get();
        // dd($operation_records);


        return view('stock.index', compact('operation_records'));
    }
    // 在庫一覧
    public function stocks()
    {
        $stocks = Stock::select('stocks.*', 'classifications.name as classification_name')->join('classifications', 'stocks.classification_id', 'classifications.id')->orderby('stocks.updated_at', 'desc')->take(20)->paginate();

        return view('stock.stocks', compact('stocks'));
    }

    // 格納先一覧
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
    public function suppliers()
    {


        $suppliers = Supplier::orderby('updated_at', 'desc')->paginate(20);

        return view('stock.suppliers', compact('suppliers'));
    }


    // 在庫編集
    public function stock_edit($stock_id)
    {
        $stock = Stock::where('id', $stock_id)->first();
        $classifications = Classification::all();
        $processes = Process::all();

        return view('stock.edit.stocks', compact('stock', 'classifications', 'processes'));
    }


    // 在庫作成
    public function create_stocks()
    {
        return view('stock.create.stocks');
    }
    // 格納先作成
    public function create_stock_storages()
    {
        $locations = Location::all();
        foreach ($locations as $location) {
            $storage_address_count = StorageAddress::where('location_id', $location->id)->count();
            $location->address_count = $storage_address_count;
        }

        return view('stock.create.stock_storages', compact('locations'));
    }

    // 場所作成
    public function store_location(Request $request)
    {
        dd('実行');
        
        $location_name = $request->location_name;

        // 名前が送信されていない場合
        if (!$location_name) {

            return redirect()->back();
        }

        // 既に同じ名前で存在する場合
        $location = Location::where('name', $location_name)->first();
        if($location){
            return redirect()->back();
        }

        $location = new Location();
        $location->name = $location_name;

        return redirect()->back();
    }
    // アドレス作成
    public function store_storage_address(Request $request){

        $location_id = $request->location_id;
        $address = $request->address;

        dd('実行');
    }

    // 取引先作成
    public function create_suppliers()
    {

        return view('stock.create.suppliers');
    }
    // 取引先編集
    public function supplier_edit()
    {
    }
}
