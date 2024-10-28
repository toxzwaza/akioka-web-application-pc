<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieMemo;
use App\Models\MovieTag;
use App\Models\MovieTagCategory;
use App\Models\User;
use App\Services\Method;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class NewMovieController extends Controller
{
    //
    public function index(Request $request)
    {

        if (!Method::isLogin()) {
            Method::msg('info', 'ログインをしてください。');
            session()->put('bef_url', 'movie2');
            return redirect()->route('login');
        }

        $text = $request->search_text;

        try {
            if ($text) {
                $keywords = preg_split('/[ 　]+/u', $text);
                $movies = MovieMemo::query();
                foreach ($keywords as $keyword) {
                    $movies = $movies->where('movie_memos.memo', 'like', '%' . $keyword . '%')->where('movie_memos.del_flg', 0);
                }

                $movies = $movies->select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->join('movies', 'movies.id', '=', 'movie_memos.movie_id')->join('movie_tags', 'movie_tags.id', '=', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tags.movie_tag_category_id', '=', 'movie_tag_categories.id')->where('movies.file_path', '!=', null)->distinct()->orderby('created_at', 'desc')->paginate(20);
            } else {
                $movies = Movie::select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->join('movie_tags', 'movie_tags.id', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tag_categories.id', 'movie_tags.movie_tag_category_id')->where('movies.file_path', '!=', null)->where('movies.del_flg', 0)->orderby('created_at', 'desc')->orderby('movie_tag_id', 'asc')->paginate(20);
            }


            foreach ($movies as $movie) {
                $movie_memos_count = MovieMemo::where('movie_id', $movie->id)->count();
                $movie->memos_count = $movie_memos_count;
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e]);
        }





        return Inertia::render('Movie/Index', ['movies' => $movies, 'search_text' => $text]);
    }

    public function searchMovie(Request $request)
    {
        $text = $request->search_text;
        try {
            if ($text) {
                $keywords = preg_split('/[ 　]+/u', $text);
                $movies = MovieMemo::query();
                foreach ($keywords as $keyword) {
                    $movies = $movies->where('movie_memos.memo', 'like', '%' . $keyword . '%')->where('movie_memos.del_flg', 0);
                }

                $movies = $movies->select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->join('movies', 'movies.id', '=', 'movie_memos.movie_id')->join('movie_tags', 'movie_tags.id', '=', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tags.movie_tag_category_id', '=', 'movie_tag_categories.id')->where('movies.file_path', '!=', null)->distinct()->orderby('created_at', 'desc')->paginate(20);
            } else {
                $movies = Movie::select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->join('movie_tags', 'movie_tags.id', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tag_categories.id', 'movie_tags.movie_tag_category_id')->where('movies.file_path', '!=', null)->where('movies.del_flg', 0)->orderby('created_at', 'desc')->orderby('movie_tag_id', 'asc')->paginate(20);
            }


            foreach ($movies as $movie) {
                $movie_memos_count = MovieMemo::where('movie_id', $movie->id)->count();
                $movie->memos_count = $movie_memos_count;
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e]);
        }


        return response()->json($movies);
    }

    public function show($movie_id)
    {

        if (!Method::isLogin()) {
            Method::msg('info', 'ログインをしてください。');

            session()->put(['bef_url' => 'movie', 'movie_id' => $movie_id]);
            return redirect()->route('login');
        }


        $movie = Movie::select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->where('movies.id', $movie_id)->join('movie_tags', 'movie_tags.id', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tag_categories.id', 'movie_tags.movie_tag_category_id')->first();

        $movie_tag_id = $movie->movie_tag_id;
        $con_movies = Movie::where('movie_tag_id', $movie_tag_id)->where('id', '<>', $movie->id)->where('del_flg', 0)->take(10)->get();


        // メモを取得
        $movie_memos = MovieMemo::select('movie_memos.*', 'users.name as user_name')->where('movie_id', $movie->id)->join('users', 'users.id', 'movie_memos.user_id')->orderby('movie_memos.created_at', 'desc')->get();



        // カテゴリー一覧を取得
        $movie_categories = MovieTagCategory::all();

        // タグ一覧を取得
        $movie_tags = MovieTag::where('movie_tag_category_id', $movie->category_id)->get();

        return Inertia::render('Movie/Show', ['movie' => $movie, 'con_movies' => $con_movies, 'movie_categories' => $movie_categories, 'movie_tags' => $movie_tags]);
    }

    public function create()
    {
        if (!Method::isLogin()) {
            Method::msg('info', 'ログインをしてください。');
            session()->put('bef_url', 'movie2');
            return redirect()->route('login');
        }
        // カテゴリー一覧を取得
        $movie_categories = MovieTagCategory::all();

        return Inertia::render('Movie/CreateMovie', ['movie_categories' => $movie_categories]);
    }
    public function store(Request $request)
    {

        $title = $request->title;
        $file_path = $request->file_path ?? '';
        $file = $request->file('file');
        $tag_id = $request->tag_id;
        $description = $request->description;




        if (!$file_path && !$request->has('file')) {
            return response()->json(['status' => 'ng', 'msg' => 'ファイルが送信できませんでした。']);
        }

        if ($file) {

            $timestamp = now()->timestamp;
            $temp_file_path = $file->storeAs('public/movie', $timestamp . '.' . $file->getClientOriginalExtension());
            $file_path = str_replace('/', '\\', '//192.168.0.72/' . $temp_file_path);
        }



        $movie = new Movie();
        $movie->name = $title;
        $movie->memo = $description;
        $movie->movie_tag_id = $tag_id;
        $movie->save();

        // RPAサーバーへリクエスト
        $url = "http://192.168.0.142:5000/movie/youtube_upload?id={$movie->id}&file_path=" . urlencode($file_path) . "&title=" . urlencode($title) . "&description=" . urlencode($description);


        // YoutubeAPIサーバへリクエスト
        $client = new Client();
        try {
            $response = $client->request('GET', $url);
            $responseArray = json_decode($response->getBody(), true);
            $movie->file_path = $responseArray['youtube_id'];
            $movie->save();
        } catch (RequestException $e) {
            // エラーハンドリング
            return response()->json(['status' => 'ng', 'msg' => 'YouTube APIへのリクエストに失敗しました。']);
        }


        return to_route('movie2.show', ['movie_id' => $movie->id]);
    }

    public function getMemos($movie_id)
    {

        // メモを取得
        $memos = MovieMemo::select('movie_memos.*', 'users.name as user_name')->where('movie_id', $movie_id)->join('users', 'users.id', 'movie_memos.user_id')->orderby('movie_memos.created_at', 'desc')->get();

        return response()->json($memos);
    }

    public function addMemo(Request $request)
    {
        $movie_id = $request->movie_id;
        $memo = $request->memo;
        $video_time = $request->video_time;
        $user_id = session('user.id');


        if ($movie_id && $memo && $video_time) {
            $movie = new MovieMemo();
            $movie->memo = $memo;
            $movie->movie_id = $movie_id;
            $movie->user_id = $user_id;
            $movie->time = $video_time;
            $movie->save();
        }
        $user_name = User::where('id', '=', $user_id)->first()->name;

        return response()->json(['status' => 'ok']);
    }

    public function deleteMemo($memo_id)
    {
        $memo = MovieMemo::find($memo_id);
        if ($memo) {
            $memo->delete();
        }
        return response()->json(['status' => 'ok']);
    }
    public function saveMemo(Request $request)
    {
        $status = 'ok';

        $memo_id = $request->memo_id;
        $new_memo_text = $request->new_memo_text;

        $memo = MovieMemo::find($memo_id);
        if ($memo) {
            $memo->memo = $new_memo_text;
            $memo->save();
        } else {
            $status = 'ng';
        }

        return response()->json(['status' => $status]);
    }
}
