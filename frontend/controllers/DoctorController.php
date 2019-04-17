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

    function actionGetDoctors($id = null)
    {
        $sql = "select b.id as uid,b.username,a.name,a.hospital_id,a.id as doctor_id,a.avatar,a.doctor_type,a.role from " . DoctorInfos::tableName() . " as a left join " . User::tableName() . " as b on a.uid=b.id ";
        $sql .=" where 1 and a.recommend=1 ";
        if ($id) {
            $sql .= " and a.id='{$id}'";
        }
        return [
            'code' => 1,
            'data' => \Yii::$app->db->createCommand($sql)->queryAll()
        ];
    }

    function actionRecommend()
    {
        return [
            'code' => 1,
            'data' => DoctorInfos::find()->where(['recommend' => 1, 'status' => 1])->with(['hospital' => function ($query) {
                $query->select('id,hospital_name,city,address,levels,province,area,grade,recommend,status,tel');
            }])->with(['relatedUser'=>function($query){
                $query->select('username as phone,id');
            }])->asArray()->all(),
        ];
    }
}