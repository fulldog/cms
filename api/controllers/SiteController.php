<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 18:10
 */

namespace api\controllers;

use api\models\Vote;
use api\service\Output;
use api\service\WechatApi;
use common\models\Article;
use common\models\Course;
use common\models\CourseCate;
use common\models\CourseChild;
use common\models\CoursePassword;
use common\models\VoteChild;
use Yii;
use api\models\form\SignupForm;
use common\models\User;
use api\models\form\LoginForm;
use yii\web\IdentityInterface;
use yii\web\Response;
use common\models\Options;

class SiteController extends \yii\rest\ActiveController
{
    public $modelClass = "common\models\Article";

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;//默认浏览器打开返回json
        return $behaviors;
    }

    public function actions()
    {
        return [];
    }

    public function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'login' => ['POST'],
            'register' => ['POST'],
        ];
    }

    /**
     * 首页
     */
    public function actionIndex()
    {
//        $data['allCate'] = CourseCate::find()->select(['name', 'id', 'alias_name'])->all();
        // 推荐课程
        $data['banner'] = [];
        $banner = Options::find()->asArray()->where(['name' => 'index'])->select('value')->one();
        if ($banner) {
            $banner = json_decode($banner['value'], true);
            foreach ($banner as $value) {
                if ($value['status']) {
                    $data['banner'][] = Yii::$app->request->getHostInfo() . $value['img'];
                }
            }
        }
        $data['recommend']['Course'] = Course::find()->select(['title', 'id', 'thumb', 'price', 'tags'])->where(['recommend' => 1, 'status' => 1])->limit(5)->all();
        foreach ($data['recommend']['Course'] as &$item) {
            $item['tags'] = Course::$_tags[$item['tags']] ?? "";
            $item['userCount'] = CoursePassword::find()->select('id')->where(['course_id' => $item['id'], 'status' => 1])->count();
            if ($item['thumb']) {
                $item['thumb'] = Yii::$app->request->getHostInfo() . $item['thumb'];
            }
        }
//        $data['recommend']['News'] = Article::find()->select(['title', 'id', 'thumb'])->where(['flag_recommend' => 1, 'status' => 1])->limit(5)->all();

        # 新闻中心
        $data['list']['News'] = Article::find()->select(['title', 'id', 'thumb', 'updated_at','desc'])->orderBy(['updated_at' => SORT_DESC])->limit(5)->all();
        foreach ($data['list']['News'] as &$item) {
            if ($item['thumb']) {
                $item['thumb'] = Yii::$app->request->getHostInfo() . $item['thumb'];
            }
        }
        $data['list']['Course'] = Course::find()->select(['title', 'id', 'thumb', 'updated_at', 'price', 'tags'])
            ->asArray()
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(5)->all();

        foreach ($data['list']['Course'] as &$item) {
            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->count();
            $item['tags'] = Course::$_tags[$item['tags']] ?? "";
            $item['userCount'] = CoursePassword::find()->select('id')->where(['course_id' => $item['id'], 'status' => 1])->count();
            if ($item['thumb']) {
                $item['thumb'] = Yii::$app->request->getHostInfo() . $item['thumb'];
            }
        }

        $data['vote'] = Vote::find()->select(['id', 'title', 'end_time', 'img', 'pv'])->orderBy(['id' => SORT_DESC])->where(['>', 'end_time', time()])->asArray()->one();
        if ($data['vote']) {
            $data['vote']['userCount'] = VoteChild::find()->where(['vid' => $data['vote']['id']])->count();
            if ($data['vote']['img']) {
                $data['vote']['img'] = Yii::$app->request->getHostInfo() . $data['vote']['img'];
            }
        }
        if (Yii::$app->request->get('openid')) {
            $data['userInfo']['myCourse'] = [];
        }

        return Output::out($data);
    }

    public function actionSearch()
    {
        $type = Yii::$app->request->get('type');
        $keyword = Yii::$app->request->get('keyword');
        $data = [
            'course' => [],
            'vote' => [],
        ];
        if ($keyword) {
            if (!$type) {
                $data['course'] = Course::find()->where(['like', 'title', $keyword])->select(['title', 'id', 'thumb', 'price', 'tags'])->limit(10)->asArray()->all();
                $data['vote'] = Vote::find()->where(['like', 'title', $keyword])->select(['title', 'id', 'img', 'pv', 'vote_count', 'desc'])->limit(10)->asArray()->all();
            } elseif ($type == 'course') {
                $data['course'] = Course::find()->where(['like', 'title', $keyword])->select(['title', 'id', 'thumb', 'price', 'tags'])->limit(10)->asArray()->all();
            } elseif ($type == 'vote') {
                $data['vote'] = Vote::find()->where(['like', 'title', $keyword])->select(['title', 'id', 'img', 'pv', 'vote_count', 'desc'])->limit(10)->asArray()->all();
            }
            if (isset($data['course'])) {
                foreach ($data['course'] as &$item) {
                    if ($item['thumb']) {
                        $item['thumb'] = Yii::$app->request->getHostInfo() . $item['thumb'];
                    }
                }
            }
            if (isset($data['vote'])) {
                foreach ($data['vote'] as &$item) {
                    if ($item['img']) {
                        $item['img'] = Yii::$app->request->getHostInfo() . $item['img'];
                    }
                }
            }
        }
        return Output::out($data);
    }

    /**
     * 登录授权
     * @return array
     */
    public function actionLogin()
    {
        $code = Yii::$app->request->post('code');
        if (!$code) {
            return Output::out([], 0, 'code not found');
        } else {
            $info = (new WechatApi())->getOpenByCode($code);
            if ($info['openid'] ?? '') {
                $user = \api\models\User::findIdentityByAccessToken($info['openid']);
                if (!$user) {
                    $signupForm = new SignupForm();
                    $signupForm->setAttributes([
                        'username' => $info['openid'],
                        'password' => $info['openid'],
                        'access_token' => $info['openid']
                    ]);
                    $signupForm->signup();
                }
                return Output::out(['openid' => $info['openid']]);
            }
            return Output::out([], $info['errcode'] ?? 0, $info['errmsg'] ?? 'fail');
        }
    }

    /**
     * 登录
     *
     * POST /login
     * {"username":"xxx", "password":"xxxxxx"}
     *
     * @return array
     */
    public function Login()
    {
        $loginForm = new LoginForm();
        $loginForm->setAttributes(Yii::$app->getRequest()->post());
        if ($user = $loginForm->login()) {
            if ($user instanceof IdentityInterface) {
                return [
                    'accessToken' => $user->access_token,
                    'expiredAt' => Yii::$app->params['user.apiTokenExpire'] + time()
                ];
            } else {
                return $user->errors;
            }
        } else {
            return $loginForm->errors;
        }

    }

    /**
     * 注册
     *
     * POST /register
     * {"username":"xxx", "password":"xxxxxxx", "email":"x@x.com"}
     *
     * @return array
     */
    public function Register()
    {
        $signupForm = new SignupForm();
        $signupForm->setAttributes(Yii::$app->getRequest()->post());
        if (($user = $signupForm->signup()) instanceof User) {
            return [
                "success" => true,
                "username" => $user->username,
                "email" => $user->email
            ];
        } else {
            return [
                "success" => false,
                "error" => $signupForm->getErrors()
            ];
        }
    }

}
