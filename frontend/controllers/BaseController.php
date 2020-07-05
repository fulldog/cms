<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/4
 * Time: 16:33
 */

namespace frontend\controllers;


use common\models\Options;
use yii\web\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

class BaseController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                //使用ComopositeAuth混合认证
                'class' => CompositeAuth::className(),
                'optional' => [
                    'detail',//无需access-token的action
                    'banner',
                    'index'
                ],
                'authMethods' => [
                    HttpBasicAuth::className(),
                    HttpBearerAuth::className(),
                    [
                        'class' => QueryParamAuth::className(),
                        'tokenParam' => 'access-token',
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'detail' => ['GET'],
                ],
            ],
        ]);
    }

    /**
     * 获取banner
     * @return array
     */
    public function actionBanner()
    {
        $type = \Yii::$app->request->get('type', 'index');
        $info = Options::findOne(['name' => $type]);
        $data = [];
        if ($info) {
            $data = \Qiniu\json_decode($info->value, true);
        }
        return $this->outPut($data);
    }

    public function outPut($data = [], $code = 1, $msg = 'success')
    {
        return [
            'data' => $data,
            'code' => $code,
            'msg' => $msg
        ];
    }
}