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

    protected $hostipal_code = 'hostipal_code';
    protected $hostipal_name = 'hostipal_name';
    protected $params = [];
    protected $logs = [];
    protected $hasException = false;

    function redisLogs($string){
        $path = \Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'apilog' . DIRECTORY_SEPARATOR . 'redisKeys';
        if (!file_exists($path)) {
            $this->createDir($path);
        }
        $path .= DIRECTORY_SEPARATOR . date('Y-m') . '.log';
        @file_put_contents($path, $string . "\r\n", FILE_APPEND | LOCK_EX);
    }


    function logs($data = [])
    {
        $path = \Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'apilog' . DIRECTORY_SEPARATOR . $this->hostipal_name;
        if (!file_exists($path)) {
            $this->createDir($path);
        }
        if ($this->hasException){
            $fileName = date('Y-m-d') . 'error.log';
        }else{
            $fileName = date('Y-m-d') . 'success.log';
        }
        $path .= DIRECTORY_SEPARATOR . $fileName;
        @file_put_contents($path, json_encode([
                'logs' => $this->logs,
                'params' => $this->params,
                'diy_msg' => $data,
            ],JSON_UNESCAPED_UNICODE) . "\r\n", FILE_APPEND | LOCK_EX);
    }

    function curl($api)
    {
        $client = new Client(['timeout' => 10]);
        $res = [];
        $this->logs['Origin-result'] = $this->logs['Exception'] = '';
        try {
            $response = $client->post($api, ['form_params' => $this->params]);
            $this->logs['Origin-result'] = trim($response->getBody()->getContents(), "\xEF\xBB\xBF");
            $res = \GuzzleHttp\json_decode($this->logs['Origin-result'], true);
        } catch (\Exception $e) {
            $this->logs['Exception'] = $e->getMessage();
//            $this->stdout('result:'.$this->logs['Exception'].PHP_EOL);
        }
        $this->logs['response'] = $res;
        return $res;
    }

    /**
     * @param string $time
     * @param int $day
     * @return false|string
     */
    function getBeforeDay(int $day, string $time = null)
    {
        $date = $time ?? date('Y-m-d');
        $type = $day > 0 ? '+' : '-';
        return date('Y-m-d', strtotime($type . abs($day) . ' day', strtotime($date)));
    }

    function createDir($str)
    {
        $arr = explode(DIRECTORY_SEPARATOR, $str);
        if (!empty($arr)) {
            $path = '';
            foreach ($arr as $k => $v) {
                $path .= $v . DIRECTORY_SEPARATOR;
                if (!file_exists($path)) {
                    mkdir($path, 0777);
                    chmod($path, 0777);
                }
            }
        }
    }
}