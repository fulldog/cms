<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2018/12/27
 * Time: 22:10
 */

namespace common\helpers;


use Codeception\Module\Yii1;

class CommonHelpers
{
    /**
     * @param array $data
     * @param string $path
     * @return string json
     */
    static function base64ToImg(array $data, $path=''){
        $_path = \Yii::getAlias('@uploads');
        if (!$path && !\Yii::$app->user->isGuest){
            $path = $_path.DIRECTORY_SEPARATOR.\Yii::$app->user->identity->username;
        }else{
            $path = $_path.DIRECTORY_SEPARATOR.$path;
        }

        self::mkdirs($path);

        $imgs_url = [];
        foreach ($data as $v){
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/',$v,$res)) {
                $type = $res[2];
                $fineName = md5($v);
                if (file_put_contents($path.DIRECTORY_SEPARATOR.$fineName.'.'.$type,base64_decode(str_replace($res[1],'', $v)))) {
                    $imgs_url[] = $path.DIRECTORY_SEPARATOR.$fineName.'.'.$type;
                }
            }else{
                $imgs_url[] = $v;
            }
        }
        return json_encode($imgs_url);
    }

    public static function mkdirs($path)
    {
        if (!file_exists($path))
        {
            self::mkdirs(dirname($path));
            mkdir($path, 0777,true);
        }
        return true;
    }

}