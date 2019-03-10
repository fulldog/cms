<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;

use common\models\doctors\DoctorInfos;
use common\models\doctors\DoctorPatients;
use common\models\Options;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\Json;


class PatientController extends BaseController
{

    const PAGE_SIZE = 50;

    function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'create' => ['POST'],
                    'transfer' => ['POST'],
                ],
            ],
//            'access' => [
//                'only' => ['create', 'transfer', 'transfer_list', 'detail'],
//            ]
        ]);
    }

    /**
     * 创建病人
     * @return array
     */
    function actionCreate()
    {
        $_post = Yii::$app->request->post();
        $_post['doctor_id'] = $this->_getUid();
        $_post['hospital_id'] = DoctorInfos::getHospitalIdByUid($_post['doctor_id']);

        $model = new DoctorPatients();
        if ($model->load($_post, '') && $model->save()) {
            return [
                'data' => $model->toArray(),
                'code' => 1,
                'msg' => 'succ'
            ];
        }
        return [
            'code' => 0,
            'msg' => $model->getErrors()
        ];
    }

    /**
     * 转移病人
     * @return array
     */
    function actionTransfer()
    {
        $_post = Yii::$app->request->post();
        if (!$_post['patient_id'] || !$_post['hospital_id']) {
            return [
                'code' => 0,
                'msg' => '参数错误:hospital_id'
            ];
        }
        $patient = DoctorPatients::findOne(['id' => $_post['patient_id']]);
//        $patient->is_transfer = 0;
        $patient->hospital_id = $_post['hospital_id'];
        $patient->doctor_id = 0;
        if ($patient->update()) {
            return [
                'data' => $patient->toArray(),
                'code' => 1,
                'msg' => 'succ'
            ];
        }
        return [
            'code' => 0,
            'msg' => 'error'
        ];
    }


    /**
     *我的病人/转给wode
     */
    function actionTransfer_list($is_ransfer = false, $page = 0)
    {
        if ($is_ransfer == 'is_ransfer') {
            $is_ransfer = true;
        }
        return [
            'data' => DoctorPatients::getPatientsByDoctorId($this->_getUid(), $page, $is_ransfer),
            'code' => 1,
            'msg' => ''
        ];
    }

    /**
     * 病人明细
     * @return array
     */
    function actionDetail($patient_id)
    {
//        $patient_id = Yii::$app->request->get('patient_id');
        return [
            'data' => DoctorPatients::findOne(['id' => $patient_id]),
            'code' => 1,
            'msg' => ''
        ];
    }

    function actionPayLog($patient_id, $date)
    {
        $patient = DoctorPatients::find()->where(['id' => $patient_id])->with('hospital')->one();
        $post = [
            'sign' => md5($patient->id_number . $patient->hospital->code),
            'start_time' =>strtotime($date),
            'end_time'=>strtotime($date)+1*24*3600-1,
            'id_card'=>$patient->id_number,
            'hospital_code'=>$patient->hospital->code,
            'page'=>1,
            'limit'=>self::PAGE_SIZE
        ];

        return $this->runPayLog($post);
    }

    function runPayLog($post){
        $api = Options::findOne(['name'=>'api_url'])->value;
        $client = new Client();
        return \GuzzleHttp\json_decode($client->post($api,$post)->getBody()->getContents());
    }
}