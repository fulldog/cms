<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/8
 * Time: 22:04
 */

namespace api\controllers;


use common\models\Course;
use common\models\CourseCate;
use common\models\CourseChild;
use common\models\CoursePassword;
use yii\rest\Controller;
use api\service\AuthService;
use api\service\Output;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

class CourseController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                //使用ComopositeAuth混合认证
                'class' => CompositeAuth::className(),
                'optional' => [
//                    'index',//无需access-token的action
//                    'list'
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
        $data['recommend'] = Course::find()->select(['title', 'id', 'thumb', 'price'])->where(['recommend' => 1, 'status' => 1])->limit(5)->asArray()->all();
        foreach ($data['recommend'] as &$item) {
            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->count();
        }
        $data['category'] = CourseCate::find()->select(['id', 'name', 'alias_name'])->asArray()->all();
        return Output::out($data);
    }

    public function actionList()
    {
        $cid = \Yii::$app->request->get('cid');
        $data = Course::find()->select(['title', 'id', 'thumb', 'price'])->where(['cid' => $cid, 'status' => 1])->limit(4)->asArray()->all();
        foreach ($data as &$item) {
            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->count();
        }
        return Output::out($data);
    }

    public function actionDetail()
    {
        $id = \Yii::$app->request->get('id');
        $data = Course::find()->select(['id', 'title', 'desc', 'wechat_img', 'thumb', 'video', 'price'])->where(['id' => $id, 'status' => 1])->asArray()->one();
        if ($data) {
            $have = false;
            if (!\Yii::$app->user->isGuest) {
                $uid = \Yii::$app->user->identity->getId();
                $have = CoursePassword::findOne(['user_id' => $uid, 'course_id' => $id]);
            }
            $data['chlidList'] = CourseChild::find()->select(['id', 'title', 'video', 'thumb', 'video'])->where(['course_id' => $id])->asArray()->all();
            if ($data['price'] && !$have) {
                foreach ($data['chlidList'] as &$item) {
                    $item['video'] = '';
                }
            }
        }
        return Output::out($data);
    }

    public function actionOrder()
    {
        $uid = \Yii::$app->user->identity->getId();
        $id = \Yii::$app->request->get('id');
        $password = \Yii::$app->request->post('password');
        if (CoursePassword::findOne(['password' => $password, 'course_id' => $id, 'user_id' => $uid])) {
            return Output::out([], 0, '请勿重复订阅');
        }
        if (CoursePassword::findOne(['password' => $password, 'course_id' => $id, 'user_id' => 0, 'status' => 0]) && CoursePassword::updateAll(['user_id' => $uid, 'status' => 1], ['password' => $password, 'course_id' => $id])) {
            return Output::out([], 1, '订阅成功');
        } else {
            return Output::out([], 0, '密码不正确');
        }
    }
}