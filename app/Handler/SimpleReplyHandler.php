<?php

use \EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class SimpleReplyHandler implements EventHandlerInterface{
    /**
     * @param mixed $message
     *
     * @return string
     */
    public function handle($message = null)
    {
        switch ($message['MsgType']) {
            case 'event':
                return '收到事件消息';
                break;
            case 'text':
                return "收到一条文字消息\n消息内容是 " . $message['Content'] . "\n用户的openid是 " . $message['FromUserName'];
                break;
            case 'image':
                return "收到图片消息\n资源ID = " . $message['MediaId']
                    . "\n图片链接 = " . $message['PicUrl'];
                break;
            case 'voice':
                $result =  "收到语音消息\n资源ID = " . $message['MediaId']
                    . "\n语音格式为: " . $message['Format'];
                if(isset($message['Recognition '])){
                    $result .= "\n识别内容为:" . $message['Recognition'];
                } else {
                    $result .= "\n未能识别出内容";
                }
                return $result;
                break;
            case 'video':
                return "收到视频消息\n资源ID = " . $message['MediaId']
                    . "\n缩略图ID = " . $message['ThumbMediaId'];
                break;
            case 'shortvideo':
                return "收到小视频消息\n资源ID = " . $message['MediaId']
                    . "\n缩略图ID = " . $message['ThumbMediaId'];
                break;
            case 'location':
                return "收到坐标消息\n位置信息->".$message['Label']
                    . "\n经度:" . $message['Location_Y'] . "\n维度:" . $message['Location_X']
                    . "\n缩放:".$message['Scale'];
                break;
            case 'link':
                return '收到链接消息';
                break;
            // ... 其它消息
            default:
                return '收到其它消息';
                break;
        }
    }
}