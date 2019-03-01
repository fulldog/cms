<?php
/**
 * 全局函数文件
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\ArrayHelper;

if (!function_exists('url')) {
    function url($url = '', $scheme = false)
    {
        return Url::to($url, $scheme);
    }
}
if (!function_exists('he')) {
    function he($text)
    {
        return Html::encode($text);
    }
}
if (!function_exists('ph')) {
    function ph($text)
    {
        return HtmlPurifier::process($text);
    }
}
if (!function_exists('t')) {
    function t($message, $params = [], $category = 'app', $language = null)
    {
        return Yii::t($category, $message, $params, $language);
    }
}
if (!function_exists('param')) {
    function param($name, $default = null)
    {
        return ArrayHelper::getValue(Yii::$app->params, $name, $default);
    }
}
if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (!function_exists('image')) {

    /**
     *
     * @param $src
     * @return string
     */
    function image($src)
    {
        return '';
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed $args
     * @return void
     */
    function dd(...$args)
    {

        foreach ($args as $x) {
            \yii\helpers\VarDumper::dump($x, 10, true);
        }

        die(1);
    }

    function hasLogin()
    {
        $uid = \Yii::$app->getUser()->getId();
        return empty($uid) ? 0 : $uid;
    }

    function getUsername(){
        return Yii::$app->user->isGuest;
    }
}

/**
 * @desc 数据导出到excel(csv文件)
 * @param $filename 导出的csv/xsl文件名称 如date("Y年m月j日")
 * @param array $tileArray 所有列名称
 * @param array $dataArray 所有列数据
 */
function exportToExcel($tileArray=[], $dataArray=[],$filename=''){
    ini_set('memory_limit','512M');
    ini_set('max_execution_time',0);
    ob_end_clean();
    ob_start();

    if (!$filename){
        $filename = date('Y-m-d').'.xls';
    }
    $filename_arr = pathinfo($filename);
    if ($filename_arr['extension']=='csv'){
        header("Content-Type: text/csv");
    }else{
        header( "Content-Type: application/vnd.ms-excel; name='excel'" );
    }
    header( "Content-type: application/octet-stream" );
    header("Content-Disposition:filename=".$filename);
    $fp=fopen('php://output','w');
    fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));//转码 防止乱码(比如微信昵称(乱七八糟的))
    fputcsv($fp,$tileArray);
    $index = 0;
    foreach ($dataArray as $item) {
        if($index==1000){
            $index=0;
            ob_flush();
            flush();
        }
        $index++;
        fputcsv($fp,$item);
    }

    ob_flush();
    flush();
    ob_end_clean();
    exit();
}