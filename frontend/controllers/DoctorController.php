<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;

use Codeception\Module\Yii1;
use common\models\doctors\DoctorInfos;
use frontend\models\User;
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
        $uid = $this->uid;
        $model = DoctorInfos::findOne(['uid' => $uid]);
        if (!$model) {
            $model = new DoctorInfos();
            $model->uid = $uid;
        }
        if ($model->load($_post, '') && $model->save()) {
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

    function actionGetDoctors()
    {
        $id = $this->get('id');
        $sql = "select b.uid,b.username,a.name,a.hospital_id,a.id,a.doctor_type,a.role from " . DoctorInfos::tableName() . " as a left join " . User::tableName() . " as b on a.uid=b.id ";
        if ($id) {
            $sql .= " where a.id='{$id}'";
        }
        return [
            'code' => 1,
            'data' => \Yii::$app->db->createCommand($sql)->queryAll()
        ];
    }
}