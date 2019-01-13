<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;

use common\models\doctors\DoctorInfos;
use yii\helpers\ArrayHelper;

class DoctorController extends BaseController
{

    function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'submit_info' => ['POST'],
                ],
            ],
//            'access' => [
//                'only' => ['submit'],
//            ]
        ]);
    }

    /**
     * 完善信息表单
     * Parma:{name,doctor_type,role,hospital_location,hospital_name,certificate(base64)}
     */
    function actionSubmit()
    {
        $_post = \Yii::$app->request->post();
        if (!$_post['hospital_id']){
            return [
                'code' => 0,
                'msg' => '缺少参数：hospital_id'
            ];
        }
        $uid = $this->_getUid();
        $model = DoctorInfos::findOne(['uid'=>$uid]);
        $msg = '添加成功';
        if (!$model){
            $msg = '修改成功';
            $model = new DoctorInfos();
        }
        if ($res = $this->_setValForObj($model, $_post)) {
            $this->_save($res);
            return [
                'data' => $res->toArray(),
                'code' => 1,
                'msg' => $msg
            ];
        }
        return [
            'code' => 0,
            'msg' => 'error'
        ];
    }
}