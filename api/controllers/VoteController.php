<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/8
 * Time: 23:11
 */

namespace api\controllers;

use yii\rest\Controller;
use api\service\AuthService;
use api\service\Output;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

class VoteController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                //使用ComopositeAuth混合认证
                'class' => CompositeAuth::className(),
                'optional' => [
                    'index',//无需access-token的action
                    'list'
                ],
                'authMethods' => [
                    HttpBasicAuth::className(),
                    HttpBearerAuth::className(),
                    [
                        'class' => AuthService::className(),
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
//                'actions' => [
//                    'info' => ['GET'],
//                ],
            ],
        ]);
    }

    public function actionIndex()
    {

    }

    public function actionDetail()
    {
    }
}