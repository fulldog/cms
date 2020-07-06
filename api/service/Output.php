<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/7
 * Time: 0:56
 */

namespace api\service;


class Output
{

    /**
     * @param array $data
     * @param int $code
     * @param string $msg
     * @return array
     */
    public static function out($data = [], $code = 1, $msg = 'success')
    {
        return [
            'data' => $data,
            'code' => $code,
            'msg' => $msg
        ];
    }

}