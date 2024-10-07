<?php

namespace App\Services;

use GuzzleHttp\Client;
use \GuzzleHttp\Psr7;


class Line
{
    private static $API = "https://notify-api.line.me/api/notify";
    private static $token = "UilkhmFzxCsLh7JiaptEEwWNy3LI2pD9fogNConcNxN";

    public static function sendMessage(string $msg)
    {

        # LINE Notifyメッセージ送信API
        $client = new Client();
        $res = $client->request('POST', self::$API, [
            'headers' => ['Authorization' => 'Bearer ' . self::$token,],
            'http_errors' => false,
            'multipart' => [
                [
                    'name' => 'message',
                    'contents' => $msg
                ]
            ]
        ]);

        # 戻り値をJSONに変換
        $res_json = json_decode($res->getBody());

        # リクエスト成功(ステータスコード：200）
        if ($res_json->status == 200) {
            return "OK";
        }
        # リクエスト失敗
        else {
            return "NG: " . $res_json->message;
        }
    }

    public static function sendImageMessage(string $msg, string $img_path)
    {

        # LINE Notifyメッセージ送信API
        $client = new Client();

        if (strpos($img_path, 'https') !== false) {
            $filePath = $img_path;
        } else {
            $filePath = str_replace('/', '\\', $img_path);

            $dir = null;

            if (!(file_exists(public_path($filePath)) || file_exists(storage_path($filePath)))) {
                
                return;
            } else if (file_exists(public_path($filePath))) {
                $dir = 'public';
            } else if (file_exists(storage_path($filePath))) {
                $dir = 'storage';
            }
            switch ($dir) {
                case 'public':
                    $filePath = public_path($filePath);
                    break;
                case 'storage':
                    $filePath = storage_path($filePath);
                    break;
                default:
                    dd($dir);
                    return;
                    break;
            }
        }
 
        




        $res = $client->request('POST', self::$API, [
            'headers' => ['Authorization' => 'Bearer ' . self::$token,],
            'http_errors' => false,
            'multipart' => [
                [
                    'name' => 'message',
                    'contents' => $msg
                ],
                [
                    'name' => 'imageFile',
                    'contents' => Psr7\Utils::tryFopen($filePath, 'r')
                ]
            ]
        ]);

        # 戻り値をJSONに変換
        $res_json = json_decode($res->getBody());

        # リクエスト成功(ステータスコード：200）
        if ($res_json->status == 200) {
            return "OK";
        }
        # リクエスト失敗
        else {
            return "NG: " . $res_json->message;
        }
    }
}
