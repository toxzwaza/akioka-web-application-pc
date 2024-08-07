<?php

namespace App\Http\Controllers;

use App\Models\MovieTag;
use App\Models\StorageAddress;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    //
    public function getAddress(Request $request){
        $location_id = $request->location_id;

        if($location_id){
            $storage_addresses = StorageAddress::where('location_id', $location_id)->orderby('address','asc')->get();
            
            return response()->json($storage_addresses);
        }

        
    }

    public function getMovieTags(Request $request){
        $movie_category_id = $request->movie_category_id;

        $movie_tags = MovieTag::where('movie_tag_category_id',$movie_category_id)->get();

        return response()->json($movie_tags);
    }

    public function getSuppliers(Request $request){
        $keyword = $request->keyword;

        if(!$keyword){
            return;
        }

        $suppliers = Supplier::where('name','like',"%$keyword%")->orWhere('tel','like',"%$keyword%")->orWhere('fax','like',"%$keyword%")->take(20)->get();

        return response()->json($suppliers);
    }
}
