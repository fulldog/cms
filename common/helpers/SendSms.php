<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 18:37
 */

namespace common\helpers;


use Overtrue\EasySms\EasySms;

class SendSms
{
    static function Send($phone, $code)
    {
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,
            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                // 默认可用的发送网关
                'gateways' => [
                    'qcloud'//
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => \Yii::getAlias('@runtime') . '/easy-sms.log',
                ],
                'yunpian' => [
                    'api_key' => '824f0ff2f71cab52936axxxxxxxxxx',
                ],
                'aliyun' => [
                    'access_key_id' => '',
                    'access_key_secret' => '',
                    'sign_name' => '',
                ],
                'qcloud' => [
                    'sdk_app_id' => '1400179951', // SDK APP ID
                    'app_key' => '31633ba331a05ccae2ff7fac862bbf8b', // APP KEY
//                    'sign_name' => 'test', // 短信签名，如果使用默认签名，该字段可缺省（对应官方文档中的sign）
                ],
            ],
        ];

        try {
            $result = (new EasySms($config))->send($phone, [
                'template' => 'SMS_001',
                'data' => [
                    'code' => $code
                ],
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $e) {
            $result = [
                $e->getResults(),
                $e->getExceptions(),
                $e->getException('gateways'),
                $e->getLastException()
            ];// 返回所有 API 的结果，结构同上
        }

        return $result;
    }
}