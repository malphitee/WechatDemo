<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Text;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{

    public function handler()
    {
        $app = $this->getApplication();

//        $app->server->push(\SimpleReplyHandler::class);
        $app->server->push(\TulingRobotHandler::class,Message::TEXT);

        $response = $app->server->serve();

        // 将响应输出
        return $response;

    }



    public function process123()
    {
        $app = $this->getApplication();

        $app->server->push(\SimpleReplyHandler::class);

        $response = $app->server->serve();

        // 将响应输出
        return $response; // Laravel 里请使用：return $response;
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
            [
                "type" => "click",
                "name" => "今日歌曲",
                "key"  => "V1001_TODAY_MUSIC"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
//                    [
//                        "type" => "view",
//                        "name" => "搜索",
//                        "url"  => "http://www.soso.com/"
//                    ],
//                    [
//                        "type" => "view",
//                        "name" => "视频",
//                        "url"  => "http://v.qq.com/"
//                    ],
                    [
                        "type" => "click",
                        "name" => "",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];

        $app->menu->create($buttons);
    }


    public function sendText()
    {
        $message = new Text('Hello world!');

        $app = $this->getApplication();

        $openId = 'oh_w1wPjL2x13uJfIaGqQhBH6Yp8';

        $result = $app->customer_service->message($message)->to($openId)->send();
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
