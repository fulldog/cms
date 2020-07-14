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
    use lmcTrait;

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
        $data['recommend'] = Course::find()->select(['title', 'id', 'thumb', 'price', 'tags', 'banner'])->where(['recommend' => 1, 'status' => 1])
            ->limit(5)->asArray()->all();
        foreach ($data['recommend'] as &$item) {
            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->count();
            $item['tags'] = Course::$_tags[$item['tags']] ?? '';
            $item['thumb'] = $this->getHostUrl($item['thumb']);
            $item['banner'] = $this->getHostUrl($item['banner']);
            $item['userCount'] = CoursePassword::find()->select('id')->where(['course_id' => $item['id'], 'status' => 1])->count();
        }
        $data['category'] = CourseCate::find()->select(['id', 'name', 'alias_name', 'img', 'img_chose'])->asArray()->all();
        foreach ($data['category'] as &$item) {
            $item['img'] = $this->getHostUrl($item['img']);
            $item['img_chose'] = $this->getHostUrl($item['img_chose']);
        }
        return Output::out($data);
    }

    public function actionList()
    {
        $page = \Yii::$app->request->get('page', 1);
        $pageSize = \Yii::$app->request->get('pageSize', 4);
        $cid = \Yii::$app->request->get('cid');
        $data = Course::find()->select(['title', 'id', 'thumb', 'price', 'tags'])->where(['status' => 1])->andFilterWhere(['cid' => $cid])
            ->offset(($page - 1) * $pageSize)->limit($pageSize)->asArray()->all();
        foreach ($data as &$item) {
            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->count();
            $item['thumb'] = $this->getHostUrl($item['thumb']);
            $item['userCount'] = CoursePassword::find()->select('id')->where(['course_id' => $item['id'], 'status' => 1])->count();
        }
        return Output::out($data);
    }

    public function actionDetail()
    {
        $id = \Yii::$app->request->get('id');
        $data = Course::find()->select(['id', 'title', 'desc', 'wechat_img', 'tags', 'thumb', 'video', 'price', 'banner'])->where(['id' => $id, 'status' => 1])->asArray()->one();
        if ($data) {
            $have = false;
            if (!\Yii::$app->user->isGuest) {
                $uid = \Yii::$app->user->identity->getId();
                $have = CoursePassword::findOne(['user_id' => $uid, 'course_id' => $id]);
            }
            !empty($data['wechat_img']) && $data['wechat_img'] = $this->getHostUrl($data['wechat_img']);
            !empty($data['thumb']) && $data['thumb'] = $this->getHostUrl($data['thumb']);
            !empty($data['banner']) && $data['banner'] = $this->getHostUrl($data['banner']);
            !empty($data['video']) && $data['video'] = $this->getHostUrl($data['video']);
            $data['chlidList'] = CourseChild::find()->select(['id', 'title', 'video', 'thumb'])->where(['course_id' => $id])->asArray()->all();
            foreach ($data['chlidList'] as $k => &$item) {
                $item['thumb'] = $this->getHostUrl($item['thumb']);
                if ($data['price']) {
                    $item['video'] = $this->getHostUrl($item['video']);
                    if ($k != 0 && !$have) {
                        $item['video'] = '';
                    }
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