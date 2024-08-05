<?php
namespace App\Services;

class Method{

    public static function msg($info, $msg){

        session()->flash($info, $msg);
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
}