<?php
namespace App\Services;

class Method{

    public static function msg($status="info", $msg){

        session()->flash('message', $msg);
        session()->flash('status', $status);
    }
    
    public static function isLogin(){
        $is_success = false;

        if (session('user')) {

            $is_success = true;
        }
        return $is_success;
    }
    public static function errorMsg(){
        self::msg('error','エラーが発生しました。管理者へ連絡してください。');
    }

    public static function checkNull($target){
        if(!$target){
            self::errorMsg();
            return redirect()->back();
        }
    }
}