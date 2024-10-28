<?php

namespace App\Http\Controllers;

use App\Models\MovieTag;
use App\Models\MovieTagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MovieCategoryAndTagController extends Controller
{
    //
    public function index(){
        // $movie_categories = MovieTagCategory::all();

        return Inertia::render('Movie/CategoryAndTag');
    }
    public function getCategories(){
        $movie_categories = MovieTagCategory::all();

        return response()->json($movie_categories);
    }
    public function getTags($category_id){

        $tags = MovieTag::where('movie_tag_category_id',$category_id)->get();

        return response()->json($tags);

    }

    public function create_category(Request $request){
        $name = $request->name;
        $color = $request->color;

        $category = new MovieTagCategory();
        $category->name = $name;
        $category->accent_color = $color;
        $category->save();

        return response()->json(['status' => 'ok']);
        
    }
    public function create_tag(Request $request){
        $category_id = $request->category_id;
        $name = $request->name;
        $color = $request->color;

        $tag = new MovieTag();
        $tag->movie_tag_category_id = $category_id;
        $tag->name = $name;
        $tag->accent_color = $color;
        $tag->save();

        return response()->json(['status' => 'ok']);

    }
}
