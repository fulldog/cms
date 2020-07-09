<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/6
 * Time: 23:50
 */

namespace api\controllers;


use api\service\AuthService;
use api\service\Output;
use common\models\Article;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

class NewsController extends \yii\rest\Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                //使用ComopositeAuth混合认证
                'class' => CompositeAuth::className(),
                'optional' => [
//                    'index',//无需access-token的action
//                    'detail'
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
        $page = abs((int)\Yii::$app->request->get('page', 1));
        $list = Article::find()->where(['status' => 1])
            ->select(['id', 'title', 'thumb', 'sub_title', 'scan_count', 'created_at'])
            ->limit(10)->offset(10 * ($page - 1))->all();
        return Output::out($list);
    }

    public function actionDetail()
    {
        $id = \Yii::$app->request->get('id');
        $data = \api\models\Article::findOne(['id' => $id, 'status' => 1]);
        if ($data) {
            Article::updateAll(['scan_count' => $data->scan_count + 1], ['id' => $id]);
        }
        return Output::out($data);
    }
}