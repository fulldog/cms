<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/3/19
 * Time: 22:37
 */

namespace console\controllers;


use GuzzleHttp\Client;
use yii\console\Controller;


class Task extends Controller
{

    const POST = "POST";
    const GET = "GET";

    protected $hostipal_code = 'not-found';
    protected $params = [];
    protected $logs = [];

    function logs($data = [])
    {

        $path = \Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'apilog' . DIRECTORY_SEPARATOR . $this->hostipal_code;

        if (!file_exists($path)) {
            $this->createDir($path);
        }
        $path .=  DIRECTORY_SEPARATOR.date('Y-m-d') . '.log';
        @file_put_contents($path, var_export([
                'line'=>'-----------------------------------------------------------------------------------------------------',
                'logs' => $this->logs,
                'params' => $this->params,
                'user_msg' => $data,
                'line-end'=>'-----------------------------------------------------------------------------------------------------',
            ], true) . "\r\n", FILE_APPEND | LOCK_EX);

    }

    function curl($api)
    {
        $this->stdout('connecting--api:'.$api.PHP_EOL);
        $client = new Client([['timeout' => 5]]);
        $res = [];
        try {
            $response = $client->post($api, ['form_params' => $this->params]);
            $result = $response->getBody()->getContents();
            $this->stdout('result:'.$result.PHP_EOL);
            $this->logs['origin-result'] = $result;
            $res = \GuzzleHttp\json_decode(trim($result, "\xEF\xBB\xBF"), true);
        } catch (\Exception $e) {
            $this->logs['Exception'] = $e->getMessage();
        }
        $this->logs['response'] = $res;
        return $res;
    }

    /**
     * @param int $day
     * @return false|string
     */
    function getBeforeDay(int $day){
        return date('Y-m-d',strtotime('-'.$day.' day',strtotime(date('Y-m-d'))));
    }

    function createDir($str)
    {
        $arr = explode(DIRECTORY_SEPARATOR, $str);
        if(!empty($arr))
        {
            $path = '';
            foreach($arr as $k=>$v)
            {
                $path .= $v.DIRECTORY_SEPARATOR;
                if (!file_exists($path)) {
                    mkdir($path, 0777);
                    chmod($path, 0777);
                }
            }
        }
    }
}