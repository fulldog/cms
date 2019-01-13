<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;


use common\models\doctors\DoctorInfos;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;

class MyController extends BaseController
{

    function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                ],
            ],
//            'access' => [
//                'only' => [],
//            ]
        ]);
    }

    function actionWallet(){
        return [
            'code'=>1,
            'msg'=>''
        ];
    }

    function actionGetme(){
        return ArrayHelper::merge(['is_complete'=>$this->getDoctor()],Yii::$app->user->identity);
    }

}