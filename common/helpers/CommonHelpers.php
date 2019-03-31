<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2018/12/27
 * Time: 22:10
 */

namespace common\helpers;

use common\models\doctors\DoctorHospitals;
use common\models\doctors\DoctorInfos;

class CommonHelpers
{
    /**
     * @param array $data
     * @param string $path
     * @return string json
     */
    static function base64ToImg(array $data, $path = '')
    {
        $_path = \Yii::getAlias('@uploads');
        $www = '/uploads/';
        if (!$path && !\Yii::$app->user->isGuest) {
            $path = $_path . DIRECTORY_SEPARATOR . \Yii::$app->user->identity->username;
            $www .= \Yii::$app->user->identity->username;
        } else {
            $path = $_path . DIRECTORY_SEPARATOR . $path;
            $www .= $path;
        }

        self::mkdirs($path);

        $imgs_url = [];
        foreach ($data as $v) {
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $v, $res)) {
                $type = $res[2];
                $fineName = md5($v);
                if (file_put_contents($path . DIRECTORY_SEPARATOR . $fineName . '.' . $type, base64_decode(str_replace($res[1], '', $v)))) {
                    $imgs_url[] = $www . '/' . $fineName . '.' . $type;
                }
            } else {
                $imgs_url[] = $v;
            }
        }
        return json_encode($imgs_url);
    }


    static function base64ToImgOne($str, $path = '')
    {
        if (is_array($str)){
            $str = array_pop($str);
        }
        $_path = \Yii::getAlias('@uploads');
        $www = '/uploads/';
        if (!$path && !\Yii::$app->user->isGuest) {
            $path = $_path . DIRECTORY_SEPARATOR . \Yii::$app->user->identity->username;
            $www .= \Yii::$app->user->identity->username;
        } else {
            $path = $_path . DIRECTORY_SEPARATOR . $path;
            $www .= $path;
        }
        self::mkdirs($path);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $str, $res)) {
            $type = $res[2];
            $fineName = md5($str);
            if (file_put_contents($path . DIRECTORY_SEPARATOR . $fineName . '.' . $type, base64_decode(str_replace($res[1], '', $str)))) {
                $img_url = $www . '/' . $fineName . '.' . $type;
                return $img_url;
            }
        }
        return $str;
    }

    public static function mkdirs($path)
    {
        if (!file_exists($path)) {
            self::mkdirs(dirname($path));
            mkdir($path, 0777, true);
        }
        return true;
    }

    static function getDoctorByHid($hid)
    {
        $res = [];
        $alls = DoctorInfos::find()->select(['name', 'id'])->where(['hospital_id' => $hid])->asArray()->all();
        if (!empty($alls)) {
            foreach ($alls as $v) {
                $res[$v['id']] = $v['name'];
            }
        }
        return $res;
    }

    static function getChats(){
        $hospital_id = \Yii::$app->user->identity->hospital_id;
        $data['超级管理员'] = ['admin'=>'admin'];

        $hospitals = DoctorHospitals::find()->all();

        if (!empty($hospitals)){
            foreach ($hospitals as $v){
                $data['医院列表']['hp'.$v->id] = $v->hospital_name;
                $data[$v->hospital_name] = [];
            }
        }

        $query = DoctorInfos::find();
        if ($hospital_id){
            $query->andFilterWhere(['hospital_id'=>$hospital_id]);
        }
        $doctor = $query->with('hospital')->all();
        if (!empty($doctor)){
            foreach ($doctor as $vv){
                if (!$vv->hospital['hospital_name'])continue;
                $data[$vv->hospital['hospital_name']][$vv->id] = $vv->name;
            }
        }
        return $data;

    }
}