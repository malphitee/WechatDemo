<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Text;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{

    public function handler()
    {
        $app = $this->getApplication();
        $server = $app->server;
        $message = $server->getMessage();
        $user = $message['FromUserName'];
        if ('event' === $message['MsgType']) {
            //接受到一个事件消息,判断消息id,发往对应的handler
            //v1.0 接收到key = Say_Hello ,调用图灵机器人
            switch ($message['EventKey']){
                case 'SAY_HELLO':
                    //向缓存中写入记录,时长15分钟,key为用户open_id,value为tuling
                    if(Cache::has($user)){
                        $this->sendText("嗯,我在这里",$user);
                    } else{
                        $this->sendText("你好啊,我来陪你聊天了!",$user);
                    }
                    Cache::put($user,'tuling',15);
                    break;
                case 'SAY_BYE':
                    //清除缓存中的值
                    Cache::forget($user);
                    $this->sendText("Bye-bye~",$user);
                    break;
                default:
                    break;
            }
        }
        if(Cache::has($user) && 'tuling' === Cache::get($user)){
            $server->push(\TulingRobotHandler::class, Message::TEXT);
            Cache::put($user,'tuling',15);
        } else {
            $server->push(\SimpleReplyHandler::class);
        }
        $response = $app->server->serve();

        // 将响应输出
        return $response;

    }

    public function getMenu()
    {
        $app = $this->getApplication();
        $list = $app->menu->list();
        dump($list);
    }

    public function setMenu()
    {
        $app = $this->getApplication();

        $buttons = [
//            [
//                "type" => "click",
//                "name" => "今日歌曲",
//                "key"  => "V1001_TODAY_MUSIC"
//            ],
            [
                "name" => "陪聊机器人",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "Hello!",
                        "key" => "SAY_HELLO"
                    ],
                    [
                        "type" => "click",
                        "name" => "Bye-Bye!",
                        "key" => "SAY_BYE"
                    ]
                ],
            ],
        ];

        $app->menu->create($buttons);
    }


    private function sendText($message,$openId)
    {
        $message = new Text($message);
        $app = $this->getApplication();
        $result = $app->customer_service->message($message)->to($openId)->send();
        \Log::info('Send a text');
        \Log::info($result);
    }

    public function getUserList()
    {
        $app = $this->getApplication();
        $arr_user = $app->user->list($nextOpenId = null);
        return $arr_user;

    }

    public function batchSendMessage()
    {
        $message = new Text('群发测试6!');

        $app = $this->getApplication();

        $arr_user = ($this->getUserList())['data']['openid'];

        $result = $app->broadcasting->sendMessage($message);

        dump($result);
    }
}
