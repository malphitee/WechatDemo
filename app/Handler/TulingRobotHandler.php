<?php

use \EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class TulingRobotHandler implements EventHandlerInterface
{
    /**
     * @param mixed $message
     *
     * @return string
     */
    public function handle($message = null)
    {
        try {

            $client = new \GuzzleHttp\Client();
            $api_url = 'http://www.tuling123.com/openapi/api';
            $api_key = env('TULING_KEY');
            $params = [
                'key' => $api_key,
                'info' => $message['Content'],
                'userid' => str_replace('_', '', $message['FromUserName'])
            ];

            $request = $client->request('POST', $api_url, ['json' => $params]);

            $status_code = $request->getStatusCode();

            if (200 === $status_code) {
                $response_body = $request->getBody()->getContents();

                return $this->process($response_body);
            } else {
                return '查询失败!';
            }
        } catch (\Exception $e){
            \Log::error($e);
        }

    }

    private function process($response_body)
    {
        \Log::info($response_body);
        $result = json_decode($response_body);
        \Log::info('===========================================');
        \Log::info($response_body);
        \Log::info('-------------------------------------------');
        \Log::info($result);
        \Log::info('===========================================');
        return $response_body;
        switch ($result['code']) {
            case 100000 :
                //文本类
                return $result['text'];
                break;
            case 200000:
                //链接类
                return $result['text'] . "<br>" . $result['url'];
            case 302000:
                //新闻类
                $message = $result['text'] . "<br>";
                $data = $result['list'];
                foreach ($data as $item) {
                    $content = "";
                    $content .= '-----------------';
                    $content .= "标题:" . $item['article'] . "<br>" . "来源:" . $item['source'] . "<br>"
                        . "详情:" . $item['detailurl'] . "<br>";
                    $content .= '-----------------';
                    $message .= $content;
                }
                return $message;

        }
    }
}