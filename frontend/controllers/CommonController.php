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
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'send_sms'=>[self::POST]
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,// 设置 actions 的操作是允许访问还是拒绝访问
                        'roles' => ['?'], // @ 当前规则针对认证过的用户， ？所有用户均可访问
                    ],
                ],
            ]
        ]);
    }

    function actionSend_sms($phone){

       return SendSms::Send(17621962204,1111);

        $time = time();
        if ($info = SmsLog::find()->where(['phone'=>$phone])->andFilterWhere('>=','created_at',strtotime(date('Y-m-d',$time)))->orderBy(['id'=>SORT_DESC])->asArray()->all()){
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
        if ($model->save()){
            //todo 短信接口
            $res = SendSms::Send($model->phone,$model->code);
            return [
                'code'=>1,
                'msg'=>'短信发送成功',
                'phoneCode'=>$model->code
            ];
        }
        return [
            'code'=>0,
            'msg'=>'响应超时',
        ];
    }
}