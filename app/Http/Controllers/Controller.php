<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $app;

    protected function getApplication(){
        if (isset($this->app)){
            return $this->app;
        } else {
            $app_id = config('wechat.official_account')['default']['app_id'];
            $secret = config('wechat.official_account')['default']['secret'];
            $token  = config('wechat.official_account')['default']['token'];

            $config = [
                'app_id' => $app_id,
                'secret' => $secret,
                'token'  => $token,

                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file' => __DIR__.'/wechat.log',
                ],
            ];

            $this->app = Factory::officialAccount($config);

            return $this->app;
        }
    }
}
