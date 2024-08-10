<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieMemo;
use App\Models\MovieTag;
use App\Models\MovieTagCategory;
use App\Models\User;
use App\Services\Method;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    //
    public function index(Request $request)
    {

        if (!Method::isLogin()) {
            Method::msg('info', 'ログインをしてください。');
            session()->put('bef_url', 'movie');
            return redirect()->route('login');
        }

        $text = $request->text;
        // $keywords = '';

        if (!$text) {

            $movies = Movie::select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->join('movie_tags', 'movie_tags.id', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tag_categories.id', 'movie_tags.movie_tag_category_id')->where('movies.del_flg',0)->orderby('created_at', 'desc')->orderby('movie_tag_id', 'asc')->paginate(20);
        } else {
            $keywords = preg_split('/[\s　]+/u', $text);
            $movies = MovieMemo::query();
            foreach ($keywords as $keyword) {
                $movies = $movies->where('movie_memos.memo', 'like', '%' . $keyword . '%')->where('movie_memos.del_flg', 0);
            }

            $movies = $movies->select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->join('movies', 'movies.id', '=', 'movie_memos.movie_id')->join('movie_tags', 'movie_tags.id', '=', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tags.movie_tag_category_id', '=', 'movie_tag_categories.id')->distinct()->orderby('created_at', 'desc')->paginate(20);
        }

        foreach ($movies as $movie) {
            $movie_memos_count = MovieMemo::where('movie_id', $movie->id)->count();
            $movie->memos_count = $movie_memos_count;
        }

        return view('movie.movies', compact('movies'));
    }

    public function show(Request $request)
    {
        $movie_id = $request->movie_id;

        if (!Method::isLogin()) {
            Method::msg('info', 'ログインをしてください。');

            session()->put(['bef_url'=>'movie', 'movie_id' => $movie_id]);
            return redirect()->route('login');
        }

       

        $movie = Movie::select('movies.*', 'movie_tags.name as movie_tag_name', 'movie_tag_categories.name as movie_tag_category_name', 'movie_tag_categories.accent_color as category_color', 'movie_tags.accent_color as tag_color', 'movie_tag_categories.id as category_id', 'movie_tags.id as tag_id')->where('movies.id', $movie_id)->join('movie_tags', 'movie_tags.id', 'movies.movie_tag_id')->join('movie_tag_categories', 'movie_tag_categories.id', 'movie_tags.movie_tag_category_id')->first();

        $movie_tag_id = $movie->movie_tag_id;
        $con_movies = Movie::where('movie_tag_id', $movie_tag_id)->where('id', '<>', $movie->id)->where('del_flg',0)->take(10)->get();

        // メモを取得
        $movie_memos = MovieMemo::select('movie_memos.*', 'users.name as user_name')->where('movie_id', $movie->id)->join('users', 'users.id', 'movie_memos.user_id')->orderby('movie_memos.created_at', 'desc')->get();



        // カテゴリー一覧を取得
        $movie_categories = MovieTagCategory::all();

        // タグ一覧を取得
        $movie_tags = MovieTag::where('movie_tag_category_id', $movie->category_id)->get();

        return view('movie.show', compact('movie', 'con_movies', 'movie_memos', 'movie_categories', 'movie_tags'));
    }
    public function movie_create(Request $request)
    {
        $movie_tags = MovieTag::select('movie_tags.id as tag_id', 'movie_tags.name as tag_name', 'movie_tag_categories.name as category_name')->join('movie_tag_categories', 'movie_tag_categories.id', 'movie_tags.movie_tag_category_id')->orderby('movie_tags.movie_tag_category_id', 'asc')->get();


        return view('movie.create.movie', compact('movie_tags'));
    }
    public function movie_store(Request $request)
    {
        $name = $request->name;
        $file_path = $request->file_path;
        $movie_tag_id = $request->movie_tag_id;
        $memo = $request->memo;

        if (!($name && $file_path && $movie_tag_id)) {
            Method::msg('error', 'ファイル名・YoutubeID・動画タグは必須項目です。');
            return redirect()->back();
        }
        $movie = new Movie();
        $movie->name = $name;
        $movie->file_path = $file_path;
        $movie->memo = $memo;
        $movie->movie_tag_id = $movie_tag_id;
        $movie->save();
        Method::msg('success', '動画ファイルを追加しました。');
        return to_route('movie');
    }
    public function movie_update(Request $request)
    {


        $movie_id = $request->movie_id;
        $name = $request->name;
        $memo = $request->memo;
        $movie_tag_id = $request->movie_tag_id;
        $created_at = $request->created_at;
        $file_path = $request->file_path;
        if (!$movie_id) {
            Method::msg('error', '動画IDが設定されていません。');
            return redirect()->back();
        }

        $movie = Movie::find($movie_id);
        $movie->name = $name;
        $movie->memo = $memo;
        $movie->movie_tag_id = $movie_tag_id;
        $movie->created_at = $created_at;
        $movie->file_path = $file_path;
        $movie->save();

        Method::msg('success', '更新が完了しました。');
        return redirect()->back();
    }
    public function movie_delete(Request $request)
    {
        $movie_id = $request->movie_id;
        $movie = Movie::find($movie_id);
        // $youtube_id = $movie->file_path;
        if (!$movie_id) {
            Method::errorMsg();
            return redirect()->back();
        }

        if ($movie) {
            $movie->del_flg = 1;
            $movie->save();

            Method::msg('success', '動画記録を削除しました。');
        }

        return to_route('movie');
    }
    public function movie_change_status(Request $request)
    {
        $movie_id = $request->movie_id;
        $movie = Movie::find($movie_id);
        if (!$movie_id) {
            Method::errorMsg();
            return redirect()->back();
        }

        if ($movie) {
            if ($movie->del_flg == 0) {
                $movie->del_flg = 1;
                Method::msg('success', '動画記録を非表示にしました。');
            } else {
                $movie->del_flg = 0;
                Method::msg('success', '動画記録を表示にしました。');
            }

            $movie->save();
        }

        return redirect()->back();
    }

    public function movie_tag_create()
    {
        $movie_categories = MovieTagCategory::all();

        return view('movie.create.tags', compact('movie_categories'));
    }

    public function addMemo(Request $request)
    {

        $movie_id = $request->movie_id;
        $memo = $request->memo;
        $video_time = $request->video_time;
        $user_id = session('user.id');

        // return response()->json(['status' => 'ok']);


        if ($movie_id && $memo && $video_time) {
            $movie = new MovieMemo();
            $movie->memo = $memo;
            $movie->movie_id = $movie_id;
            $movie->user_id = $user_id;
            $movie->time = $video_time;
            $movie->save();
        }
        $user_name = User::where('id', '=', $user_id)->first()->name;

        return response()->json(['status' => 'ok', 'created_at' => $movie->created_at->format('Y/m/d'), 'memo' => $movie->memo, 'time' => $movie->time, 'user_name' => $user_name, 'memo_id' => $movie->id]);
    }

    public function movie_memo_delete($memo_id)
    {
        if (!$memo_id) {
            return redirect()->back();
        }

        $memo = MovieMemo::find($memo_id);
        $memo->delete();

        Method::msg('success', 'メモを削除しました。');
        return redirect()->back();
    }
    public function movie_memo_update(Request $request)
    {
        $memo_id = $request->memo_id;
        $memo_content = $request->memo;


        if (!$memo_id) {
            return redirect()->back();
        }


        $memo = MovieMemo::find($memo_id);
        // dd($memo);
        $memo->memo = $memo_content;
        $memo->save();

        Method::msg('success', 'メモを更新しました。');
        return redirect()->back();
    }
}
