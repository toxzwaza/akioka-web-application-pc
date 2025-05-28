<?php

namespace App\Http\Controllers;

use App\Models\NotifyQueue;
use App\Models\NotifyQueueUser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    //
    public function index(){
        $notify_queues = NotifyQueue::with(['notifyQueueUsers' => function($query) {
            $query->join('users', 'users.id', '=', 'notify_queue_users.user_id')
                  ->select('notify_queue_users.notify_queue_id', 'users.name');
        }])->orderBy('id', 'desc')->get();

        foreach($notify_queues as $notify_queue){
            $notify_queue->notify_users = $notify_queue->notifyQueueUsers->pluck('name');
            unset($notify_queue->notifyQueueUsers);
        }

        return Inertia::render('Notify/Index', [
            'notify_queues' => $notify_queues,
        ]);
    }
}
