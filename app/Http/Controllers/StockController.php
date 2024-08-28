<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\Location;
use App\Models\Process;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\StorageAddress;
use App\Models\Supplier;
use App\Services\Method;
use Exception;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function test() {}
    //
    public function index()
    {
        $operation_records = InventoryOperationRecord::select('stocks.name as stock_name', 'inventory_operations.name as operation_name', 'inventory_operations.id as operation_id', 'inventory_operation_records.created_at', 'users.name as user_name', 'inventory_operation_records.quantity', 'inventory_operation_records.est_quantity')->join('inventory_operations', 'inventory_operations.id', 'inventory_operation_records.inventory_operation_id')->join('stock_storages', 'stock_storages.id', 'inventory_operation_records.stock_storage_id')->join('stocks', 'stocks.id', 'inventory_operation_records.stock_id')->join('users', 'users.id', 'inventory_operation_records.user_id')->orderby('inventory_operation_records.updated_at', 'desc')->paginate(6);
        // dd($operation_records);


        $operation_record_recent = [];
        for ($i = 0; $i < 6; $i++) {
            $recent_count = InventoryOperationRecord::whereDate('created_at', now()->subDays(5 - $i)->toDateString())
                ->count();
            $operation_record_recent[] = $recent_count;
        }
        // dd($operation_record_recent);

        // dd($operation_record_recent);


        return view('stock.index', compact('operation_records', 'operation_record_recent'));
    }
    // 在庫一覧
    public function stocks(Request $request)
    {
        $keyword = $request->keyword;
        $storage_address_id = $request->storage_address_id;

        if ($storage_address_id) {

            if ($keyword) {
                // 格納アドレスと検索キーワード
                $stocks = StockStorage::select('stocks.*')
                    ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
                    ->where('storage_address_id', $storage_address_id)
                    ->where('stocks.del_flg', 0)
                    ->where(function ($query) use ($keyword) {
                        $query->where('stocks.name', 'like', "%$keyword%")
                            ->orWhere('stocks.s_name', 'like', "%$keyword%")
                            ->orWhere('stocks.jan_code', 'like', "%$keyword%");
                    })
                    ->orderby('updated_at', 'desc')
                    ->paginate(20);

                // $stocks = Stock::select('stocks.*', 'classifications.name as classification_name')->join('classifications', 'stocks.classification_id', 'classifications.id')->where('stocks.name', 'like', "%$keyword%")->orWhere('stocks.s_name', 'like', "%$keyword%")->orWhere('stocks.jan_code', 'like', "%$keyword%")->orderby('stocks.updated_at', 'desc')->paginate(20);
            } else {
                // 格納アドレスのみ

                $stocks = StockStorage::select('stocks.*')
                    ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
                    ->where('storage_address_id', $storage_address_id)
                    ->where('stocks.del_flg', 0)
                    ->orderby('updated_at', 'desc')->paginate(20);
            }


            return view('stock.stocks', compact('stocks'));
        }


        if ($keyword) {
            // キーワード検索のみ
            $stocks = Stock::where('stocks.del_flg', 0)
                ->where('stocks.name', 'like', "%$keyword%")
                ->orWhere('stocks.s_name', 'like', "%$keyword%")
                ->orWhere('stocks.jan_code', 'like', "%$keyword%")
                ->orderby('stocks.updated_at', 'desc')->paginate(20);
        } else {
            // 検索条件なし
            $stocks = Stock::where('stocks.del_flg', 0)
                ->orderby('stocks.updated_at', 'desc')->paginate(20);
        }

        return view('stock.stocks', compact('stocks'));
    }

    // 
    public function storage_address(Request $request)
    {
        $location_id = $request->location_id;
        if ($location_id) {
            $storage_location_addresses = StorageAddress::select('storage_addresses.id as storage_address_id', 'locations.name as location_name', 'storage_addresses.address', 'storage_addresses.created_at')->join('locations', 'locations.id', 'storage_addresses.location_id')
                ->where('location_id', $location_id)
                ->orderby('address', 'asc')->paginate(20);
        } else {

            $storage_location_addresses = StorageAddress::select('storage_addresses.id as storage_address_id', 'locations.name as location_name', 'storage_addresses.address', 'storage_addresses.created_at')->join('locations', 'locations.id', 'storage_addresses.location_id')->orderby('address', 'asc')->paginate(20);
        }

        foreach ($storage_location_addresses as $sla) {
            $stock_storage_count = StockStorage::join('stocks', 'stocks.id', 'stock_storages.stock_id')
                ->join('classifications', 'stocks.classification_id', 'classifications.id')
                ->where('storage_address_id', $sla->storage_address_id)
                ->where('stocks.del_flg', 0)
                ->where('stocks.not_stock_flg', 0)
                ->count();


            $sla->count = $stock_storage_count;
        }




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

        $stock_suppliers = StockSupplier::select('stock_suppliers.id as stock_supplier_id', 'stock_suppliers.memo as stock_supplier_memo', 'suppliers.*', 'stock_suppliers.lead_time', 'stock_suppliers.act_flg')->where('stock_id', $stock_id)->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->get();



        // 在庫状況
        $stock_storages = StockStorage::select('stock_storages.id as stock_storage_id', 'quantity', 'locations.name as location_name', 'address', 'location_id', 'storage_addresses.id as storage_address_id')->join('storage_addresses', 'storage_addresses.id', 'stock_storages.storage_address_id')->join('locations', 'locations.id', 'storage_addresses.location_id')->where('stock_id', $stock_id)->get();
        // dd($stock_storages);

        try {
            $storage_addresses = StorageAddress::where('location_id', $stock_storages[0]->location_id)->orderby('address', 'asc')->get();
        } catch (Exception $e) {
            $storage_addresses = StorageAddress::where('location_id', 1)->get();
        }

        return view('stock.edit.stocks', compact('stock', 'classifications', 'processes', 'stock_storages', 'locations', 'storage_addresses', 'stock_suppliers'));
    }
    public function store_stocks(Request $request)
    {
        $stock_id = $request->stock_id;
        $stock_no = $request->stock_no;
        $name = $request->name;
        $jan_code = $request->jan_code;
        $s_name = $request->s_name;
        $img_path = $request->img_path;
        $price = $request->price;
        $url = $request->url;
        $del_flg = $request->del_flg;
        $purchase_identification_number = $request->purchase_identification_number;
        $solo_unit = $request->solo_unit;
        $org_unit = $request->org_unit;
        $main_unit_flg = $request->main_unit_flg;
        $quantity_per_org = $request->quantity_per_org;
        $classification_id = $request->classification_id;
        $deli_location = $request->deli_location;
        $process_code = $request->process_code;
        $memo = $request->memo;



        $stock = Stock::find($stock_id);
        if (!$stock) {
            $stock = new Stock();
        }
        if ($request->hasFile('upload_file')) {

            $image = $request->file('upload_file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $img_path = 'storage/' . $image->storeAs('stock', $imageName, 'stock');
        }
        $stock->name = $name;
        $stock->stock_no = $stock_no;
        $stock->s_name = $s_name;
        $stock->jan_code = $jan_code;
        $stock->url = $url;
        $stock->img_path = $img_path;
        $stock->price = $price;
        $stock->purchase_identification_number = $purchase_identification_number;
        $stock->solo_unit = $solo_unit;
        $stock->org_unit = $org_unit;
        $stock->quantity_per_org = $quantity_per_org;
        $stock->main_unit_flg = $main_unit_flg;
        $stock->classification_id = $classification_id;
        $stock->deli_location = $deli_location;
        $stock->process_code = $process_code;
        $stock->memo = $memo;
        $stock->del_flg = $del_flg ?? 0;
        $stock->save();

        Method::msg('success', '完了しました。');
        // dd($stock_id, $stock_no, $name, $jan_code, $s_name, $img_path, $url, $purchase_identification_number, $solo_unit, $org_unit, $main_unit_flg,$quantity_per_org, $classification_id, $deli_location, $process_code, $memo);

        return redirect()->back();
    }

    // 棚卸し
    public function stock_taking(Request $request)
    {
        $storage_address_id = $request->keyword ?? null;
        $storage_address_name = StorageAddress::find($storage_address_id)->address ?? '';
        $storage_addresses = StorageAddress::orderby('address', 'asc')->get();


        $stocks = StockStorage::select('stocks.*', 'stock_storages.quantity')
            ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
            ->join('classifications', 'classifications.id', 'stocks.classification_id')
            ->where('storage_address_id', $storage_address_id)
            ->where('stocks.del_flg', 0)
            ->where('stocks.not_stock_flg', 0)
            ->paginate(20);


        return view('stock.taking.stocks', compact('stocks', 'storage_addresses', 'storage_address_name'));
    }


    // 在庫作成
    public function create_stocks()
    {
        $classifications = Classification::all();
        return view('stock.create.stocks', compact('classifications'));
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
        if ($location) {
            return redirect()->back();
        }


        $location = new Location();
        $location->name = $location_name;
        $location->save();

        return redirect()->back();
    }
    // アドレス作成
    public function store_storage_address(Request $request)
    {

        $location_id = $request->location_id;
        $shelf = $request->shelf;
        $row = $request->row;
        $col = $request->col;
        $sub_row = $request->sub_row;
        // アドレス作成
        $address = $shelf . '-' . $row;
        if ($col) {
            $address = $address . '-' . $col;
        }
        if ($sub_row) {
            $address = $address . '-' . $sub_row;
        }

        // $address = $request->address;

        if (!$location_id) {
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
        $storage_address->shelf = $shelf;
        $storage_address->row = $row;
        $storage_address->col = $col;
        $storage_address->sub_row = $sub_row;
        $storage_address->save();

        return redirect()->route('stock.storage_addresses.create');
    }

    // 取引先作成
    public function create_suppliers()
    {

        return view('stock.create.suppliers');
    }
    // 取引先編集
    public function supplier_edit() {}


    public function update_stock_storage(Request $request)
    {
        $method = $request->method;
        $stock_storage_id = $request->stock_storage_id;
        $storage_address_id = $request->storage_address_id;

        if (!($method == 'change')) {
            // 同一格納先
            // アドレスもしくは、個数の変更


            $quantity = $request->quantity;
            // dd($quantity, $stock_storage_id);

            if (!$storage_address_id || !$quantity) {


                Method::msg('error', '数量が未入力の可能性があります。');
                return redirect()->back();
            }
            // dd($stock_storage_id);

            $stock_storage = StockStorage::where('id', $stock_storage_id)->first();
            if ($stock_storage) {
                $stock_storage->storage_address_id = $storage_address_id;
                $stock_storage->quantity = $quantity;
                $stock_storage->save();
            }
        } else {
            // 格納先の変更

            $stock_storage = StockStorage::where('id', $stock_storage_id)->first();
            if ($stock_storage) {
                $stock_storage->storage_address_id = $storage_address_id;
                $stock_storage->save();
            }
        }




        Method::msg('success', '更新が完了しました。');




        return redirect()->back();
    }

    public function delete_stock_storage(Request $request)
    {
        $stock_storage_id = $request->stock_storage_id;

        // dd($stock_storage_id);
        $stock_storage = StockStorage::find($stock_storage_id);
        $stock_storage->delete();

        return redirect()->back();
    }

    public function create_stock_storage(Request $request)
    {
        $stock_id = $request->stock_id;
        $storage_address_id = $request->storage_address_id;
        $quantity = $request->quantity;

        if (!($stock_id && $storage_address_id && $quantity)) {
            Method::msg('error', 'アドレスと個数を入力して再度お試しください。');
            return redirect()->back();
        }

        $stock_storage = new StockStorage();
        $stock_storage->stock_id = $stock_id;
        $stock_storage->storage_address_id = $storage_address_id;
        $stock_storage->quantity = $quantity;
        $stock_storage->save();

        Method::msg('success', '在庫保管情報を登録しました。');
        return redirect()->back();
    }

    public function stock_add_supplier(Request $request)
    {
        $stock_id = $request->stock_id;
        $supplier_id = $request->supplier_id;

        if (!($stock_id && $supplier_id)) {
            Method::errorMsg();
            return redirect()->back();
        }
        $stock_supplier = new StockSupplier();
        $stock_supplier->stock_id = $stock_id;
        $stock_supplier->supplier_id = $supplier_id;
        $stock_supplier->save();
        Method::msg('success', '得意先を紐づけました。');
        return redirect()->back();
    }
    public function store_stock_suppliers(Request $request)
    {
        $stock_supplier_id = $request->stock_supplier_id;
        $lead_time = $request->lead_time;
        $memo = $request->memo;

        Method::checkNull($stock_supplier_id);

        $stock_supplier = StockSupplier::where('id', $stock_supplier_id)->first();
        $stock_supplier->lead_time = $lead_time;
        $stock_supplier->memo = $memo;
        $stock_supplier->save();
        // dd($stock_supplier);

        Method::msg('success', '物品得意先情報を更新しました。');
        return redirect()->back();
    }
    public function delete_stock_suppliers(Request $request)
    {
        $stock_supplier_id = $request->stock_supplier_id;

        Method::checkNull($stock_supplier_id);
        $stock_supplier = StockSupplier::find($stock_supplier_id);
        $stock_supplier->delete();

        Method::msg('success', '物品得意先情報を削除しました。');
        return redirect()->back();
    }

    //  滞留品
    public function retained_stocks(){
        dd('実行');
    }
}
