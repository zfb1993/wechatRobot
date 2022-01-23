<?php
require 'vendor/autoload.php';

use Mitoop\Robot\Robot;
use Overtrue\Weather\Weather;
use Carbon\Carbon;

$config = [
    // 默认发送的分组 支持多个
    'default' => ['wecom.clock'],
    // 发送消息的服务器env 如: production/development 等
    'env' => 'production',
    // HTTP 请求的超时时间(秒)
    'timeout' => 5,
    // 机器人提供商 feishu : 飞书 / wecom : 企业微信 / dingding : 钉钉
    'channels' => [
        // 企业微信 配置解释参考飞书(企业微信没有密钥校验策略)
        'wecom' => [
            'groups' => [
                'clock' => [
                    'webhook' => 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=99934929-b7fa-4538-9489-ace54d73cf44',
                    'at' => ['所有人'],
                    'show_env' => true,
                    'timeout' => 3,
                ],
            ],
        ],
    ],
];

$robot = new Robot($config);

$today = Carbon::today()->toDateString();
//获取今天是一周的第几天
$index = Carbon::now()->dayOfWeek;
$indexTxt = '';
switch ($index){
    case 0:
        $index = '星期日';
        break;
    case 1 :
        $indexTxt = '星期一';
        break;
    case 2 :
        $indexTxt = '星期二';
        break;
    case 3 :
        $indexTxt = '星期三';
        break;
    case 4 :
        $indexTxt = '星期四';
        break;
    case 5 :
        $indexTxt = '星期五';
        break;
    case 6 :
        $indexTxt = '星期六';
        break;
}

$key = '';
$weather = new Weather($key);
$response = $weather->getLiveWeather('郑州');
$weaTxt = '';

if($response['lives'] && count($response['lives']) ){
    $info = $response['lives']['0'];

    $weaTxt = $info['weather'].',当前室外温度:'.$info['temperature'].'℃';
}

$markdown = '';
if($weaTxt){
    $markdown = "### 企业微信打卡提醒~~ \n".
        "今天是".$today.','.$indexTxt."  \n".
        "#### 郑州天气  \n ".
        "> ".$weaTxt."\n ".
        "亲亲，不要忘记企业微信打卡哦~";
}else{
    $markdown = "### 企业微信打卡提醒~~ \n
亲亲，不要忘记企业微信打卡哦~";
}

$res = $robot->sendMarkdownMsg($markdown);

var_dump($res);
