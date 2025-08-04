<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationProcess;
use App\Models\Process;
use App\Models\StockStorage;
use App\Models\StorageAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LocationController extends Controller
{
    //
    public function index()
    {

        $locations = Location::with(['processes'])
            ->select('locations.id', 'locations.name', 'locations.updated_at')
            ->leftJoin('storage_addresses', 'storage_addresses.location_id', 'locations.id')
            ->select('locations.id', 'locations.name', 'locations.updated_at', DB::raw('COUNT(storage_addresses.id) as address_count'))
            ->groupBy('locations.id', 'locations.name', 'locations.updated_at')
            ->get();
        $processes = Process::all();

        return Inertia::render('Stock/Location/Index', ['locations' => $locations, 'processes' => $processes]);
    }

    public function store(Request $request)
    {
        $status = true;

        $location_id = $request->location_id;
        $location_name = $request->location_name;
        $processes = $request->processes;

        try {
            if ($location_id) { //編集
                $location = Location::where('id', $location_id)->first();
                $location->name = $location_name;
                $location->save();
                $location_processes = LocationProcess::where('location_id', $location_id)->get();
                
                // 登録済みの管理部署を削除
                foreach ($location_processes as $location_process) {
                    $location_process->delete();
                }
            } else { //新規
                $location = new Location();
                $location->name = $location_name;
                $location->save();
            }


            foreach ($processes as $process) {
                $location_process = new LocationProcess();
                $location_process->location_id = $location->id;
                $location_process->process_id = $process;
                $location_process->save();
            }
        } catch (Exception $e) {
            $status = false;
        }
        return response()->json(['status' => $status]);
    }

    public function show(Request $request)
    {
        $location_id = $request->location_id;
        $processes = Process::all();

        $location = Location::where('id', $location_id)->first();
        $location_processes = LocationProcess::where('location_id', $location_id)->pluck('process_id');
        $storage_addresses = StorageAddress::
        leftJoin('stock_storages', 'stock_storages.storage_address_id', 'storage_addresses.id')
        // ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
        ->where('location_id', $location_id)
        ->orderBy('shelf')
        ->orderBy('row')
        ->orderBy('col')
        ->select('storage_addresses.id', 'storage_addresses.address', 'storage_addresses.shelf', 'storage_addresses.row', 'storage_addresses.col', DB::raw('COUNT(stock_storages.id) as stock_count'), 'storage_addresses.updated_at')
        ->groupBy('storage_addresses.id', 'storage_addresses.address', 'storage_addresses.shelf', 'storage_addresses.row', 'storage_addresses.col', 'storage_addresses.updated_at')
        ->get()
        ->map(function ($storage_address) {
            $storage_address->storage_address_stocks = StockStorage::where('storage_address_id', $storage_address->id)->get();
            return $storage_address;
        });

        return Inertia::render('Stock/Location/Show', ['location' => $location, 'processes' => $processes, 'location_processes' => $location_processes, 'storage_addresses' => $storage_addresses]);
    }
}
