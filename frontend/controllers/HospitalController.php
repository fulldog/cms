<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;


use common\models\doctors\DoctorHospitals;
use yii\helpers\ArrayHelper;
use Yii;

class HospitalController extends BaseController
{

    function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'add' => ['POST'],
                ],
            ],
            'access' => [
                'only' => ['add'],
            ]
        ]);
    }

    /**
     * 新增医院后，需要为医院开一个后台账号，账号规则暂时为hospital+hospital_id 密码都是123456后台那边 强制第一次登录后改密码
        add_hospital
        param: { hospital_name, province, city, area, address, grade}
        result: {hospital_account}
     */
    function actionAdd(){
        $model = new DoctorHospitals();
        $_post = \Yii::$app->request->post();
        if ($model->_load($_post) && $model->save()){
            $data['code'] = 1;
            $data['msg'] = '新增医院成功';
            $data['data']['hospital_info'] = $model->toArray();
            //创建管理员  移到后台审核操作
//            $admin = new User();
//            $admin->hospital_id = $model->id;
//            $admin->username = 'hospital'.$model->id;
//            $admin->password = 123456;
//            if (!$admin->save()){
//                $data['msg'] = '新增医院成功，但创建医院管理员失败，请联系超管处理';
//            }else{
//                $data['data']['hospital_account'] = $admin->toArray();
//            }
            return $data;
        }
        return [
            'code' => 0,
            'msg' => 'error'
        ];
    }

    /**
     * 医院搜索
     * @return array
     */
    function actionSearch()
    {
        $search_word = Yii::$app->request->get('search_word');
        $page = Yii::$app->request->get('page',1);
        return [
            'data'=>DoctorHospitals::like('hospital_name',$search_word,$page)
        ];
    }
}