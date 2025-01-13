<?php

namespace App\Http\Controllers;

use App\Models\NotifyGroup;
use App\Models\NotifyGroupUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class MessageController extends Controller
{
    //
    public function index(){
        $message_users = User::whereNotNull('email')->where('del_flg', 0)->get();
        // グループ
        $notify_groups = NotifyGroup::where('del_flg', 0)->get();
        foreach($notify_groups as $notify_group){
            $notify_group_users  = NotifyGroupUser::select('notify_group_users.*','users.name as user_name')->where('notify_group_id', $notify_group->id)->join('users', 'notify_group_users.user_id', 'users.id')->get();
            $notify_group->users  = $notify_group_users;
        }

        return Inertia::render('Message/Index', ['users' => $message_users, 'notify_groups' => $notify_groups ]);
    }

    public function getNotifyGroupUsers(){

    }

    // ライン送信用グループを作成
    public function create_group(Request $request){
        $group_users = $request->group_users;
        $group_name  = $request->group_name;
        $status = "ok";
        $msg = "";

        if(!($group_users && $group_name)){
            $status = "ng";

            return response()->json(['status' => $status]);
        }

        $notify_group = new NotifyGroup();
        $notify_group->name = $group_name;
        $notify_group->save();

        foreach($group_users as $group_user){
            $notify_group_user = new NotifyGroupUser();
            $notify_group_user->user_id = $group_user;
            $notify_group_user->notify_group_id = $notify_group->id;
            $notify_group_user->save();

            $msg = "グループの作成が完了しました。";
        }

        return response()->json(['status' => $status, 'msg' => $msg ]);
    }


    // Teamsより通知
    public function sendNotify(Request $request)
    {

        $mentionIds = $request->mentionIds;
        $message = $request->message;

        // Webhook URL（.envファイルから取得）
        $webhookUrl = env('TEAMS_WEBHOOK_URL');


        if (!$webhookUrl) {
            return response()->json(['error' => 'Webhook URLが設定されていません。'], 500);
        }

        // メンション部分を生成
        $mentions = array_map(function ($id) {
            return [
                "type" => "mention",
                "text" => "<at>{$id}</at>",
                "mentioned" => [
                    "id" => $id,
                    "name" => $id,
                ],
            ];
        }, $mentionIds);

        // メンション用テキストを生成
        $mentionText = implode(' ', array_map(fn($id) => "@<at>{$id}</at>", $mentionIds));

        // Adaptive Cardのペイロード
        $payload = [
            "type" => "message",
            "attachments" => [
                [
                    "contentType" => "application/vnd.microsoft.card.adaptive",
                    "content" => [
                        "type" => "AdaptiveCard",
                        "body" => [
                            [
                                "type" => "TextBlock",
                                "text" => $mentionText,
                                "color" => "attention",
                                "size" => "large",
                            ],
                            [
                                "type" => "TextBlock",
                                "text" => "アキオカアプリからの通知です。",
                                "color" => "default",
                                "size" => "default",
                            ],
                            [
                                "type" => "TextBlock",
                                "text" => $message,
                                "color" => "good",
                                "size" => "medium",
                                "wrap" => True  # テキストの折り返しを有効化
                            ],
                        ],
                        '"$schema"' => "http://adaptivecards.io/schemas/adaptive-card.json",
                        "version" => "1.0",
                        "msteams" => [
                            "entities" => $mentions,
                        ],
                    ],
                ],
            ],
        ];

        // リクエストを送信
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($webhookUrl, $payload);

        // 結果を返却
        if ($response->successful()) {
            return response()->json(['message' => '通知が送信されました！'], 200);
        } else {
            return response()->json(['error' => '通知の送信に失敗しました。'], $response->status());
        }
    }
}
