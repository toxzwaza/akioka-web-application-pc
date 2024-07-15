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
use App\Services\Method;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function test(){

    }
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

    // 
    public function storage_address(Request $request)
    {
        $location_id = $request->location_id;
        if($location_id){
            $storage_location_addresses = StorageAddress::select('storage_addresses.id as storage_address_id', 'locations.name as location_name', 'storage_addresses.address', 'storage_addresses.created_at')->join('locations', 'locations.id', 'storage_addresses.location_id')->where('location_id',$location_id)->paginate(20);

        }else{

            $storage_location_addresses = StorageAddress::select('storage_addresses.id as storage_address_id', 'locations.name as location_name', 'storage_addresses.address', 'storage_addresses.created_at')->join('locations', 'locations.id', 'storage_addresses.location_id')->paginate(20);
        }

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
        $locations = Location::all();
       

        // 在庫状況
        $stock_storages = StockStorage::select('stock_storages.id as stock_storage_id','quantity','locations.name as location_name','address','location_id')->join('storage_addresses','storage_addresses.id','stock_storages.storage_address_id')->join('locations','locations.id','storage_addresses.location_id')->where('stock_id', $stock_id)->get();
        // dd($stock_storages);
    
        $storage_addresses = StorageAddress::where('location_id',$stock_storages[0]->location_id)->get();

        return view('stock.edit.stocks', compact('stock', 'classifications', 'processes','stock_storages','locations','storage_addresses'));
    }
    public function store_stocks(Request $request){
        $stock_id = $request->stock_id;
        $stock_no = $request->stock_no;
        $name = $request->name;
        $jan_code = $request->jan_code;
        $s_name = $request->s_name;
        $img_path = $request->img_path;
        $url = $request->url;
        $purchase_identification_number	 = $request->purchase_identification_number;
        $solo_unit = $request->solo_unit;
        $org_unit = $request->org_unit;
        $quantity_per_org = $request->quantity_per_org;
        $classification_id = $request->classification_id;
        $deli_location = $request->deli_location;
        $process_code = $request->process_code;
        $memo = $request->memo;

        dd($stock_id, $stock_no, $name, $jan_code, $s_name, $img_path, $url, $purchase_identification_number, $solo_unit, $org_unit, $quantity_per_org, $classification_id, $deli_location, $process_code, $memo);

        
    }


    // 在庫作成
    public function create_stocks()
    {
        return view('stock.create.stocks');
    }
    // 格納先作成
    public function create_storage_addresses()
    {
        $locations = Location::all();
        foreach ($locations as $location) {
            $storage_address_count = StorageAddress::where('location_id', $location->id)->count();
            $location->address_count = $storage_address_count;
        }

        return view('stock.create.storage_address', compact('locations'));
    }

    // 場所作成
    public function store_location(Request $request)
    {
      
        
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
        $location->save();

        return redirect()->back();
    }
    // アドレス作成
    public function store_storage_address(Request $request){

        $location_id = $request->location_id;
        $address = $request->address;

        if(!$location_id || !$address){
            return redirect()->back();
        }

        // 一時的処置
        // if(!$location_id){
        //     $location_id = 2;
        // }
        // StorageAddress新規作成
        $storage_address = new StorageAddress();
        $storage_address->address = $address;
        $storage_address->location_id = $location_id;
        $storage_address->save();

        return redirect()->route('stock.storage_addresses.create');
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


    public function update_stock_storage(Request $request){


        $stock_storage_id = $request->stock_storage_id;
        $storage_address_id = $request->storage_address_id;
        $quantity = $request->quantity;

        if(!$storage_address_id || !$quantity){
            Method::msg('error','数量が未入力の可能性があります。');
            return redirect()->back();
        }
        // dd($stock_storage_id);

        $stock_storage = StockStorage::where('id',$stock_storage_id)->first();
        if($stock_storage){
            $stock_storage->storage_address_id = $storage_address_id;
            $stock_storage->quantity = $quantity;
            $stock_storage->save();
        }
        Method::msg('success','更新が完了しました。');
  
        


        return redirect()->back();

    }
}
