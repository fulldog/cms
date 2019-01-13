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
        return [
            'code'=>1,
            'msg'=>'',
            'doctors'=>DoctorInfos::findAll(),
            'users'=>User::find()->select('*')->all()
        ];
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
            'data' => ArrayHelper::merge(['is_complete'=>$this->getDoctor()],Yii::$app->user->identity),
            'code' => 1,
            'msg' => ''
        ];
    }

    function actionRegister()
    {
        $username = Yii::$app->request->post('phone');
        $password = Yii::$app->request->post('password');

        if (User::findOne(['username' => $username])) {
            return [
                'code' => 0,
                'msg' => '账号已被注册'
            ];
        } else {
            $user = new SignupForm();
            $user->username = $username;
            $user->password = $password;
            $user->email = $username.'@qq.com';

            if ($user->signup()) {
                $model = new LoginForm();
                $model->username = $username;
                $model->password = $password;
                $model->login();
                return [
                    'data' => ArrayHelper::merge(['is_complete'=>$this->getDoctor()],Yii::$app->user->identity),
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
        描述：首页含有banner、推荐医院、推荐医生。
        这三个在后台可以分别设置。是否可以放在一个接口里，还是分三个接口
        index_info
        param: {}
        result: {banners: [‘xxx.jpg’], recommend_hospitals: [],recommend_doctors: [] }
        recommend_hospitals 信息按照新增医院的信息返回
     */
    function actionInfo(){
        //banners
        $banner = BannerTypeForm::find()->where(['type' => Options::TYPE_BANNER,'name'=>'index'])->asArray()->one();
        $imgs = [];
        if (!empty($banner)){
            $imgs = \Qiniu\json_decode($banner['value']);
        }
        return [
            'banners'=>$imgs,
            'recommend_hospitals'=>DoctorHospitals::findAll(['recommend'=>1,'status'=>1]),
            'recommend_doctors'=>DoctorInfos::findAll(['recommend'=>1,'status'=>1]),
        ];
    }

    function actionError(){
        return [
            'code'=>'0',
            'msg'=>'not found!',
            'httpCode'=>404
        ];
    }
}