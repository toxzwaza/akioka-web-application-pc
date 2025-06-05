<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
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
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Http;

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

                $movies = $movies->select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->join('movies', 'movies.id', '=', 'movie_memos.movie_id')->join('movie_tags', 'movie_tags.id', '=', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tags.movie_tag_category_id', '=', 'movie_tag_categories.id')->distinct()->orderby('created_at', 'desc')->paginate(20);
            } else {
                $movies = Movie::select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')
                    ->join('movie_tags', 'movie_tags.id', 'movies.movie_tag_id')
                    ->join('movie_tag_categories', 'movie_tag_categories.id', 'movie_tags.movie_tag_category_id')
                    ->where('movies.del_flg', 0)
                    ->orderby('created_at', 'desc')
                    ->orderby('movie_tag_id', 'asc')
                    ->paginate(20);
            }


            foreach ($movies as $movie) {
                $movie_memos_count = MovieMemo::where('movie_id', $movie->id)->count();
                $movie->memos_count = $movie_memos_count;
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e]);
        }

        $base_movies =  Movie::select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->join('movie_tags', 'movie_tags.id', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tag_categories.id', 'movie_tags.movie_tag_category_id')->where('movies.del_flg', 0)->orderby('created_at', 'desc')->orderby('movie_tag_id', 'asc')->get();
        $movie_tags = MovieTag::all();
        $movie_categories = MovieTagCategory::all();

        return Inertia::render('Movie/Index', ['movies' => $movies, 'base_movies' => $base_movies, 'search_text' => $text, 'tags' => $movie_tags, 'categories' => $movie_categories]);
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
        // $movie_memos = MovieMemo::select('movie_memos.*', 'users.name as user_name')->where('movie_id', $movie->id)->join('users', 'users.id', 'movie_memos.user_id')->orderby('movie_memos.time', 'asc')->get();



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

        // ログイン者を取得
        $user = User::where('id', session('user.id'))->first();

        return Inertia::render('Movie/CreateMovie', ['movie_categories' => $movie_categories, 'user' => $user]);
    }
    public function store(Request $request)
    {
        $status = true;
        $msg = "";

        $title = $request->title;
        $created_at = $request->created_at;
        $file_path = $request->file_path ?? '';
        $youtube_id = $request->youtube_id ?? '';
        $tag_id = $request->tag_id;
        $description = $request->description;


        try {
            if ($file_path) {
                $movie = new Movie();
                $movie->name = $title;
                $movie->memo = $description === 'null' ? null : ($description ?? '');
                $movie->movie_tag_id = $tag_id;
                $movie->file_path = $file_path;
                $movie->youtube_id = $youtube_id;
                $movie->transcription_flg = 1;

                // 投稿日が記載されている場合
                if ($created_at != 'null') {
                    $movie->created_at = $created_at;
                }
                $movie->save();
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function delete(Request $request)
    {
        $status = true;

        $movie_id = $request->movie_id;
        try {
            $movie = Movie::find($movie_id);
            if ($movie) {
                $movie->delete();
            }
        } catch (Exception $e) {
            $status = false;
        }
        return response()->json(['status' => $status]);
    }

    public function getMemos($movie_id)
    {

        // メモを取得
        $memos = MovieMemo::select('movie_memos.*', 'users.name as user_name')
            ->where('movie_id', $movie_id)
            ->whereNull('transcription_flg')
            ->join('users', 'users.id', '=', 'movie_memos.user_id')
            ->orderBy('movie_memos.time', 'asc')
            ->get();

        $transcription_memos = MovieMemo::select('movie_memos.*', 'users.name as user_name')
            ->where('movie_id', $movie_id)
            ->whereNotNull('transcription_flg')
            ->join('users', 'users.id', '=', 'movie_memos.user_id')
            ->orderBy('movie_memos.time', 'asc')
            ->get();

        return response()->json(['memos' => $memos, 'transcription_memos' => $transcription_memos]);
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

    // 文字お越し待ち取得
    public function getWaitingTranscription()
    {

        // 文字お越し待ち状態で、ファイルパスが格納されているデータを取得
        $movies = Movie::select('movies.id', 'movies.file_path')
            ->where(
                'transcription_flg',
                1
            )->where('file_path', '!=', null)
            ->get();

        return response()->json($movies);
    }

    public function update(Request $request)
    {
        $status = true;

        $movie_id = $request->movie_id;
        $flg = $request->flg;
        $value = $request->value;

        try {
            $movie = Movie::find($movie_id);
            if ($movie) {
                switch ($flg) {
                    case 'file_path':
                        $movie->file_path = $value;
                        break;
                    case 'youtube_id':
                        $movie->youtube_id = $value;
                        break;
                }
                $movie->save();
            }
        } catch (Exception $e) {
            $status = false;
        }
        return response()->json(['status' => $status]);
    }
}
