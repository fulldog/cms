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
        $uid = $this->_getUid();
        $model = DoctorInfos::findOne(['uid'=>$uid]);
        if (!$model){
            $model = new DoctorInfos();
            $model->uid = $uid;
        }
        if ($model->load($_post,'') && $model->save()) {
            return [
                'data' => $model->toArray(),
                'code' => 1,
                'msg' => '修改成功'
            ];
        }
        return [
            'code' => 0,
            'msg' => $model->getErrors()
        ];
    }
}