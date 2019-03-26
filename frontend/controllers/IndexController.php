<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26 0026
 * Time: 16:53
 */

namespace frontend\controllers;

use backend\models\form\BannerForm;
use backend\models\form\BannerTypeForm;
use common\models\doctors\DoctorHospitalsQuery;
use common\models\doctors\SmsLog;
use common\models\Options;
use Yii;
use frontend\models\form\SignupForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\LoginForm;
use common\models\doctors\DoctorHospitals;
use common\models\doctors\DoctorInfos;
use common\models\doctors\DoctorPatients;
use frontend\models\User;

class IndexController extends BaseController
{

    function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'login' => ['POST'],
                    'register' => ['POST'],
                ],
            ],
            'access' => [
                'rules' => [
                    [
                        'allow' => true,// 设置 actions 的操作是允许访问还是拒绝访问
                        'roles' => ['?'], // @ 当前规则针对认证过的用户， ？所有用户均可访问
                    ],
                ],
            ]
        ]);
    }

    function actionIndex()
    {
        return Yii::$app->response->redirect('/html/');
//        return [
//            'code'=>1,
//            'msg'=>'',
//            'doctors'=>DoctorInfos::findAll(),
//            'users'=>User::find()->select('*')->all()
//        ];
    }

    /**
     * password username
     * @return array
     * @throws \Throwable
     */
    function actionLogin()
    {
        if (!hasLogin()) {
            $model = new LoginForm();
            $model->username = Yii::$app->request->post('phone');
            $model->password = Yii::$app->request->post('password');
            if (!$model->login()) {
                return [
                    'code' => 0,
                    'msg' => '账号或密码错误'
                ];
            }
        }
        return [
            'data' => ArrayHelper::merge(['is_complete' => $this->getDoctor()], Yii::$app->user->identity),
            'code' => 1,
            'msg' => ''
        ];
    }

    function checkCode($username, $code)
    {
        $sms = SmsLog::find()->where(['phone' => $username, 'code' => $code])->orderBy(['id' => SORT_DESC])->asArray()->one();
        if (empty($sms) || (time() - $sms['created_at']) > 600) {
            echo json_encode([
                'code' => 0,
                'msg' => '验证码无效或已过期'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    function actionRegister()
    {
        $username = Yii::$app->request->post('phone');
        $password = Yii::$app->request->post('password');
        $code = Yii::$app->request->post('code');

        $this->checkCode($username, $code);

        if (User::findOne(['username' => $username])) {
            return [
                'code' => 0,
                'msg' => '账号已被注册'
            ];
        } else {
            $user = new SignupForm();
            $user->username = $username;
            $user->password = $password;

            if ($user->signup()) {
                $model = new LoginForm();
                $model->username = $username;
                $model->password = $password;
                $model->login();
                return [
                    'data' => ArrayHelper::merge(['is_complete' => $this->getDoctor()], Yii::$app->user->identity),
                    'code' => 1,
                    'msg' => '注册成功',

                ];
            } else {
                return [
                    'code' => 0,
                    'msg' => $user->getErrors()
                ];
            }
        }
    }

    /**
     * 首页信息
     * 描述：首页含有banner、推荐医院、推荐医生。
     * 这三个在后台可以分别设置。是否可以放在一个接口里，还是分三个接口
     * index_info
     * param: {}
     * result: {banners: [‘xxx.jpg’], recommend_hospitals: [],recommend_doctors: [] }
     * recommend_hospitals 信息按照新增医院的信息返回
     */
    function actionInfo()
    {
        //banners
        $banner = BannerTypeForm::find()->where(['type' => Options::TYPE_BANNER, 'name' => 'index'])->asArray()->one();
        $imgs = [];
        if (!empty($banner)) {
            $imgs = \Qiniu\json_decode($banner['value']);
        }
        return [
            'code' => 1,
            'data' => [
                'banners' => $imgs,
                'recommend_hospitals' => DoctorHospitals::findAll(['recommend' => 1, 'status' => 1]),
                'recommend_doctors' => DoctorInfos::find()->where(['recommend' => 1, 'status' => 1])->with(['hospital' => function ($query) {
                    $query->select('id,hospital_name,city,address,levels,province,area,grade,recommend,status,tel');
                }])->asArray()->all(),
            ]
        ];
    }

    function actionRepassword()
    {
        $data = Yii::$app->request->post();
        if ($data['password'] != $data['password2']) {
            return [
                'code' => '0',
                'msg' => '两次输入的密码不一样',
            ];
        }

        $this->checkCode($data['phone'], $data['code']);

        $user = User::findOne(['username' => $data['username']]);
        $user->setPassword($data['password']);
        $user->generateAuthKey();
        if ($user->save()) {
            return [
                'code' => '1',
                'msg' => '修改成功',
            ];
        }
        return [
            'code' => '0',
            'msg' => '修改失败:' . $user->getFirstErrors(),
        ];
    }

    function actionLogout()
    {
        parent::actionLogout(); // TODO: Change the autogenerated stub
    }

    function actionError()
    {
        return [
            'code' => '0',
            'msg' => 'not found!',
            'httpCode' => 404
        ];
    }
}