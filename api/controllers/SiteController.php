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
    use lmcTrait;
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
        $data['allCate'] = CourseCate::find()->select(['name', 'id', 'alias_name','img','img_chose'])->asArray()->all();
        // 推荐课程
        $data['banner'] = [];
        $banner = Options::find()->asArray()->where(['name' => 'index'])->select('value')->one();
        if ($banner) {
            $banner = json_decode($banner['value'], true);
            foreach ($banner as $value) {
                if ($value['status']) {
                    $data['banner'][] = $this->getHostUrl($value['img']);
                }
            }
        }
        $data['recommend']['Course'] = Course::find()->select(['title', 'id', 'thumb', 'price', 'tags', 'banner'])->where(['recommend' => 1, 'status' => 1])->asArray()->limit(5)->all();
        foreach ($data['recommend']['Course'] as &$item) {
            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->count();
            $item['tags'] = Course::$_tags[$item['tags']] ?? "";
            $item['userCount'] = CoursePassword::find()->select('id')->where(['course_id' => $item['id'], 'status' => 1])->count();
            $item['thumb'] = $this->getHostUrl($item['banner'] ?: $item['thumb']);
        }
//        $data['recommend']['News'] = Article::find()->select(['title', 'id', 'thumb'])->where(['flag_recommend' => 1, 'status' => 1])->limit(5)->all();

        # 新闻中心
        $data['list']['News'] = Article::find()->select(['title', 'id', 'thumb', 'updated_at', 'sub_title'])->orderBy(['updated_at' => SORT_DESC])->limit(5)->all();
        foreach ($data['list']['News'] as &$item) {
            $item['thumb'] = $this->getHostUrl($item['thumb']);
        }
        $data['list']['Course'] = Course::find()->select(['title', 'id', 'thumb', 'updated_at', 'price', 'tags', 'banner'])
            ->asArray()
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(5)->all();

        foreach ($data['list']['Course'] as &$item) {
            $item['childCount'] = CourseChild::find()->where(['course_id' => $item['id']])->count();
            $item['tags'] = Course::$_tags[$item['tags']] ?? "";
            $item['userCount'] = CoursePassword::find()->select('id')->where(['course_id' => $item['id'], 'status' => 1])->count();
            $item['thumb'] = $this->getHostUrl($item['thumb']);
        }

        $data['vote'] = Vote::find()->select(['id', 'title', 'end_time', 'img', 'pv', 'banner'])->orderBy(['end_time' => SORT_DESC])
            ->where(['>', 'end_time', time()])->andWhere(['recommend' => 1])->asArray()->one();
        if ($data['vote']) {
            $data['vote']['userCount'] = VoteChild::find()->where(['vid' => $data['vote']['id']])->count();
            $data['vote']['img'] = $this->getHostUrl($data['vote']['banner'] ?: $data['vote']['img']);
        }
        if (Yii::$app->request->get('openid')) {
            $uid = Yii::$app->user->getId();
            $data['userInfo']['myCourse'] = CoursePassword::find()->where(['user_id' => $uid])->count();
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
                        $item['thumb'] = $this->getHostUrl($item['thumb']);
                    }
                }
            }
            if (isset($data['vote'])) {
                foreach ($data['vote'] as &$item) {
                    if ($item['img']) {
                        $item['img'] = $this->getHostUrl($item['img']);
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
            $appInfo = Options::find()->select(['name', 'value'])->where(['name' => 'wechat_appid'])->orWhere(['name' => 'wechat_appsecret'])->asArray()->all();
            $wechat_appid = '';
            $wechat_appsecret = '';
            foreach ($appInfo as $item) {
                if ($item['name'] == 'wechat_appid') {
                    $wechat_appid = $item['value'];
                } elseif ($item['name'] == 'wechat_appsecret') {
                    $wechat_appsecret = $item['value'];
                }
            }
            if (!$wechat_appid || !$wechat_appsecret) {
                return Output::out([], 0, 'wechat_appid/wechat_appsecret not found');
            }
            $info = (new WechatApi($wechat_appid, $wechat_appsecret))->getOpenByCode($code);
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
