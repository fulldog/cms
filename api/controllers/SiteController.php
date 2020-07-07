<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 18:10
 */

namespace api\controllers;

use api\service\Output;
use api\service\WechatApi;
use app\models\CourseCate;
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
        $allCate = CourseCate::find()->all();
    }

    /**
     * 登录授权
     * @return array
     */
    public function actionLogin()
    {
        $code = Yii::$app->request->get('code');
        if (!$code) {
            return Output::out([], 0, 'code not found');
        } else {
            $info = (new WechatApi())->getOpenByCode($code);
            $info['openid'] = 'xxxxxxxxxxx';
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
