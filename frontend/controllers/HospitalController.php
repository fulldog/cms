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
        if ($model->load($_post,'') && $model->save()){
            $data['code'] = 1;
            $data['msg'] = '新增医院成功';
            $data['data'] = $model->toArray();
            return $data;
        }
        return [
            'code' => 0,
            'msg' => $model->getErrors()
        ];
    }

    /**
     * 医院搜索
     * @return array
     */
    function actionSearch()
    {
        $search_word = Yii::$app->request->get('search_word');
        $page = Yii::$app->request->get('page',0);
        return [
            'code'=>1,
            'data'=>DoctorHospitals::like('hospital_name',$search_word,$page)
        ];
    }
}