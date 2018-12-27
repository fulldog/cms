<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26 0026
 * Time: 16:53
 */

namespace frontend\controllers;
use common\models\LoginForm;
use frontend\models\User;
use Yii;
use frontend\models\form\SignupForm;

class IndexController extends BaseController
{

    function actionIndex(){

    }

    /**
     * password username
     * @return array
     * @throws \Throwable
     */
    function actionLogin(){
        if (!hasLogin()){
            $model = new LoginForm();
            $model->username = Yii::$app->request->post('username');
            $model->password = Yii::$app->request->post('password');
            if (!$model->login()) {
                return [
                    'code'=>0,
                    'msg'=>'账号或密码错误'
                ];
            }
        }
        return [
            'data'=>Yii::$app->user->identity,
            'code'=>0,
            'msg'=>''
        ];
    }

    function actionRegister(){
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        if (User::findOne(['username'=>$username])){
            return [
                'code'=>0,
                'msg'=>'改账号已被注册'
            ];
        }else{
            $user = new SignupForm();
            $user->username = $username;
            $user->password = $password;
            $user->status = 0;
            if ($user = $user->signup()){
                return [
                    'data'=>$user,
                    'code'=>1,
                    'msg'=>'注册成功'
                ];
            }else{
                return [
                    'code'=>0,
                    'msg'=>'注册失败，请检查参数'
                ];
            }
        }
    }

    function actionLogout(){
        Yii::$app->user->logout(true);
    }
}