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
        try{
            $result = json_decode($response_body,true);
            switch ($result['code']) {
                case 100000 :
                    //文本类
                    return $result['text'];
                    break;
                case 200000:
                    //链接类
                    return $result['text'] . "\n" . $result['url'];
                case 302000:
                case 308000:
                    //新闻类与菜谱类合并处理
                    //新闻类/菜谱类回复信息需要转为图文消息发送,图文消息最大数量限制为8条
                    $data = $result['list'];
                    if (count($data) > 8) {
                        $data = array_slice($data, 0, 8);
                    }
                    $items = [];
                    foreach ($data as $item) {
                        $items[] = new \EasyWeChat\Kernel\Messages\NewsItem([
                            'title'         =>  isset($item['article'])?$item['article']:$item['name'],
                            'description'   =>  isset($item['source'])?$item['source']:$item['info'],
                            'url'           =>  $item['detailurl'],
                            'image'         =>  $item['icon']
                        ]);
                    }
                    $message = new \EasyWeChat\Kernel\Messages\News($items);
                    \Log::info($message->toXmlArray());
                    return $message;

            }
        }catch (\Exception $e){
            \Log::info($e);
        }

    }
}