<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;

use common\helpers\SendSms;
use common\models\doctors\SmsLog;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class CommonController extends BaseController
{

    function behaviors()
    {
        return [];
    }

    function actionSend_sms($phone){
        $time = time();
        if ($info = SmsLog::find()->where(['phone'=>$phone])->andFilterWhere(['>=','created_at',strtotime(date('Y-m-d',$time))])->orderBy(['id'=>SORT_DESC])->asArray()->all()){
            if (count($info)>=5 || ($time-$info[0]['created_at']<=5*60*1000)){
                return [
                    'code'=>0,
                    'msg'=>'每天只能发送5次短信,每次发送短信间隔时间为5分钟',
                ];
            }
        }
        $model = new SmsLog();
        $model->code = random_int(1000,999999);
        $model->phone = $phone;
        if ($model->save() && SendSms::TenSend($model->phone,$model->code)){
            return [
                'code'=>1,
                'msg'=>'短信发送成功',
                'phoneCode'=>$model->code
            ];
        }
        return [
            'code'=>0,
            'msg'=>$model->getErrors(),
        ];
    }
}