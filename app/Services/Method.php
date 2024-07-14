<?php
namespace App\Services;

class Method{

    public static function msg($info, $msg){

        session()->flash($info, $msg);
    }
}