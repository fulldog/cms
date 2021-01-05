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
use common\models\UserCollect;
use yii\rest\Controller;
use api\service\AuthService;
use api\service\Output;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use common\models\Options;


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
                    ],
                ],
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
        $data['recommend'] = [];
        $banner = Options::find()->asArray()->where(['name' => 'course'])->select('value')->cache(60)->one();
        if ($banner) {
            $banner = json_decode($banner['value'], true);
            foreach ($banner as $value) {
                if ($value['status']) {
                    $data['recommend'][] = [
                        'thumb' => $this->getHostUrl($value['img']),
                        'id' => $value['newsId'] ?? '',
                    ];
                }
            }
        }

//        $data['recommend'] = Course::find()->select(['title', 'id', 'thumb', 'price', 'tags', 'banner'])->where(['recommend' => 1, 'status' => 1])
//            ->limit(5)->asArray()->all();
//
//        foreach ($data['recommend'] as &$item) {
//            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->count();
//            $item['tags'] = Course::$_tags[$item['tags']] ?? '';
//            $item['thumb'] = $this->getHostUrl($item['thumb']);
//            $item['banner'] = $this->getHostUrl($item['banner']);
//            $item['userCount'] = CoursePassword::find()->select('id')->where(['course_id' => $item['id'], 'status' => 1])->count();
//        }
        $data['category'] = CourseCate::find()->select(['id', 'name', 'alias_name', 'img', 'img_chose'])->cache(60)->asArray()->all();
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
        $list = Course::find()->select(['title', 'id', 'thumb', 'price', 'tags', 'subscribe'])->where(['status' => 1])->andFilterWhere(['cid' => $cid])
            ->orderBy(['cid' => SORT_ASC])
            ->cache(60)
            ->offset(($page - 1) * $pageSize)->limit($pageSize)->all();
        $data = [];
        foreach ($list as &$it) {
            $item = $it->toArray();
            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->cache(60)->count();
            $item['thumb'] = $this->getHostUrl($item['thumb']);
            $item['userCount'] = $item['subscribe'] + $it->userCollect;
            $data[] = $item;
        }
        return Output::out($data);
    }

    public function actionDetail()
    {
        $id = \Yii::$app->request->get('id');
        $dataObj = Course::find()->select(['id', 'title', 'desc', 'cid', 'wechat_img', 'tags', 'thumb', 'video', 'price', 'banner', 'subscribe'])->where(['id' => $id, 'status' => 1])->cache(60)->one();
        $data = $dataObj->toArray();
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
            $data['subscript'] = $dataObj->userCollect + $data['subscribe'];
            $data['chlidList'] = CourseChild::find()->select(['id', 'title', 'video', 'thumb'])->where(['course_id' => $id])->cache(60)->asArray()->all();
            foreach ($data['chlidList'] as $k => &$item) {
                $item['thumb'] = $this->getHostUrl($item['thumb']);
                $item['video'] = $this->getHostUrl($item['video']);
                if ($data['price'] && !$have) {
                    $item['video'] = '';
                }
            }
            $data['others'] = call_user_func(function () use ($data) {
                $list = Course::find()->asArray()->select(['id', 'title', 'desc', 'wechat_img', 'tags', 'thumb', 'video', 'price', 'banner'])->cache(60)
                    ->where(['status' => 1, 'cid' => $data['cid']])->andWhere(['<>', 'id', $data['id']])->limit(4)->all();
                foreach ($list as &$li) {
                    $li['wechat_img'] = $this->getHostUrl($li['wechat_img']);
                    $li['video'] = $this->getHostUrl($li['video']);
                    $li['thumb'] = $this->getHostUrl($li['thumb']);
                    $li['banner'] = $this->getHostUrl($li['banner']);
                }
            });
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

    public function actionCollect()
    {
        $uid = \Yii::$app->user->identity->getId();
        $course_id = \Yii::$app->request->get('course_id');
        if (!$course_id) {
            return Output::out([], 0, '课程ID必填');
        }
        if (UserCollect::find()->where(['user_id' => $uid, 'course_id' => $course_id])->cache(60)->exists()) {
            return Output::out([], 0, '该课程已收藏');
        }

        $model = new UserCollect();
        $model->user_id = $uid;
        $model->course_id = $course_id;
        $model->updated_at = $model->created_at = time();
        if ($model->save()) {
            return Output::out($model->toArray(), 1, '收藏成功');
        } else {
            return Output::out($model->toArray(), 0, current($model->getErrors()));
        }
    }
}