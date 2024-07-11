<?php

namespace App\Http\Controllers;

use App\Models\StorageAddress;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    //
    public function getAddress(Request $request){
        $location_id = $request->location_id;

        if($location_id){
            $storage_addresses = StorageAddress::where('location_id', $location_id)->get();
            
            return response()->json($storage_addresses);
        }

        
    }
}
