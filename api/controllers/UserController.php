<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 18:10
 */

namespace api\controllers;

use api\service\AuthService;
use api\service\Output;
use common\models\Course;
use common\models\CourseChild;
use common\models\CoursePassword;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

/**
 * Class UserController
 * @package api\controllers
 *
 * 调用/register注册用户后，再次调用/login登录获取accessToken,再次访问/users?access-token=xxxxxxx访问
 */
class UserController extends \yii\rest\ActiveController
{
    public $modelClass = "api\models\User";

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                //使用ComopositeAuth混合认证
                'class' => CompositeAuth::className(),
                'optional' => [
                    'info',//无需access-token的action
                ],
                'authMethods' => [
                    HttpBasicAuth::className(),
                    HttpBearerAuth::className(),
                    [
                        'class' => AuthService::className(),
//                        'tokenParam' => 'access-token',
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'info' => ['GET'],
                ],
            ],
        ]);
    }

    public function actionSubscribe()
    {
        $uid = \Yii::$app->user->identity->getId();
        $data = CoursePassword::find()
            ->where(['p.user_id' => $uid, 'p.status' => 1])
            ->alias('p')
            ->leftJoin(Course::tableName() . ' c', 'p.course_id=c.id')
            ->select('c.id,c.title,c.desc,c.wechat_img,c.thumb,c.video,c.status,c.price,c.cid')
            ->asArray()
            ->all();
        if ($data) {
            foreach ($data as &$item) {
                $item['childCount'] = CourseChild::find()->where(['course_id'=>$item['id']])->count();
                $item['subscribeCount'] = CoursePassword::find()->where(['course_id'=>$item['id']])->count();
            }
        }
        return Output::out($data);
    }
}
