<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;


use common\models\doctors\DoctorArticle;
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
     * 新增医院
     * add_hospital
     * param: { hospital_name, province, city, area, address, grade}
     * result: {hospital_account}
     */
    function actionAdd()
    {
        $model = new DoctorHospitals();
        $_post = \Yii::$app->request->post();

        if ($model::findOne(['hospital_name' => $_post['hospital_name']])) {
            return [
                'code' => 0,
                'msg' => $_post['hospital_name'] . "已存在，请勿重复添加"
            ];
        }

        if ($model->load($_post, '') && $model->save()) {
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
     * @param string $search_word
     * @param int $page
     * @return array
     */
    function actionSearch($search_word = '', $page = 0)
    {
        return [
            'code' => 1,
            'data' => DoctorHospitals::like('hospital_name', $search_word, $page)
        ];
    }

    function actionTransfer()
    {
        return [
            'code' => 1,
            'data' => DoctorHospitals::find()->where(['transfer' => 1, 'status' => 1])->asArray()->all(),
        ];
    }

    function actionArticle($id = 0)
    {
        if ($id) {
            $info = DoctorArticle::findOne(['id' => $id, 'status' => 1]);//, 'hospital_id' => $this->getDoctor()->hospital_id
        } else {
            $info = DoctorArticle::findAll(['status' => 1]);//'hospital_id' => $this->getDoctor()->hospital_id,
        }
        return [
            'code' => 1,
            'data' => $info,
        ];
    }

    function actionGetHospitals($id = null)
    {
        return [
            'code' => 1,
            'data' => DoctorHospitals::find()->andFilterWhere(['id' => $id])->asArray()->all()
        ];
    }

    function actionRecommend()
    {
        return [
            'code' => 1,
            'data' => DoctorHospitals::findAll(['recommend' => 1, 'status' => 1]),
        ];
    }
}