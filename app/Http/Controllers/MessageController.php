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
        $server->push(\TulingRobotHandler::class, Message::TEXT);
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

    public function deleteMenu()
    {
        $app = $this->getApplication();
        $app->menu->delete();
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
