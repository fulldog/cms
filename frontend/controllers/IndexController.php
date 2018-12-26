<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26 0026
 * Time: 16:53
 */

namespace frontend\controllers;
use Yii;
use backend\tests\unit\models\LoginForm;
use frontend\models\form\SignupForm;

class IndexController extends BaseController
{

    function actionIndex(){
        return [
            'data'=>[
                'uid'=>Yii::$app->getUser()->getIdentity()->getId(),
                'username'=>Yii::$app->getUser()->getIdentity()->username
            ],
            'code'=>1,
            'msg'=>'登陆成功'
        ];
    }

    function actionLogin(){
        if (!hasLogin()){
            $model = new LoginForm();
            if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
                return [
                    'data'=>[
                        'uid'=>Yii::$app->getUser()->getIdentity()->getId(),
                        'username'=>Yii::$app->getUser()->getIdentity()->username
                    ],
                    'code'=>1,
                    'msg'=>'登陆成功'
                ];
            }
        }
        return [
            [],
            'code'=>0,
            'msg'=>'请勿重复登陆'
        ];
    }

    function actionRegister(){
        $model = new SignupForm();
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return [
                            'data'=>[
                                'uid'=>Yii::$app->getUser()->getIdentity()->getId(),
                                'username'=>Yii::$app->getUser()->getIdentity()->username
                            ],
                            'code'=>1,
                            'msg'=>'注册成功'
                            ];
                }
            }
        }
    }
}