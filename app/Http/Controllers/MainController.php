<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Method;
use Illuminate\Http\Request;

class MainController extends Controller
{
    //
    public function index(){

        return view('home');
    }
    public function login(){
        $users = User::all();
        return view('login',compact('users'));
    }
    public function login_store(Request $request){
        $user_id = $request->user_id;

        if(!$user_id){
            Method::msg('error','user_idが選択されていません。');
            return redirect()->back();
        }
        $user = User::find($user_id);
        if(!$user){
            Method::msg('error','ユーザー情報を取得できませんでした。');
            return redirect()->back();
        }

        session()->put('user',$user);
        Method::msg('success','ログインが完了しました。');
        return redirect()->route('home');

    }
    public function logout(){
        $user_id = session('user.id');
        if(!$user_id){
            Method::msg('error','ログインされていません。');
            return redirect()->back();
        }

        session()->forget('user');
        Method::msg('success','ログアウトが完了しました。');
        return redirect()->route('home');
    }
}
