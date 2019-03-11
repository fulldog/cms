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

    const PAGE_SIZE = 20;

    function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'create' => [self::POST],
                    'transfer' => [self::POST],
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

        $doctor = DoctorInfos::findOne(['uid'=>$this->uid]);
        $_post['doctor_id'] = $doctor->id;
        $_post['hospital_id'] = $doctor->hospital_id;

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
                'msg' => '参数错误'
            ];
        }
        $patient = DoctorPatients::findOne(['id' => $_post['patient_id']]);
        if ($patient->is_transfer>0){
            return [
                'code' => 0,
                'msg' => '转诊病人不能再次转移！'
            ];
        }
        $patient->is_transfer = 1;
        $patient->transfer_doctor = $patient->doctor_id;
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
            'msg' => $patient->getFirstError()
        ];
    }


    /**
     *我的病人
     */
    function actionList($page = 1)
    {$this->uid= 7;
        return [
            'data' => DoctorPatients::find()->where(['doctor_id' => $this->uid])
                ->orWhere(['transfer_doctor'=>$this->uid])
                ->offset(self::PAGE_SIZE * ($page - 1))->asArray()->all(),
            'code' => 1,
            'msg' => ''
        ];
    }

    /**
     *转给wode
     */
    function actionTransfer_list($page = 1)
    {
        return [
            'data' => DoctorPatients::find()->where(['doctor_id' => $this->uid, 'is_transfer' => 1])
                ->offset(self::PAGE_SIZE * ($page - 1))->asArray()->all(),
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
            'start_time' => $date,
            'end_time' => date('Y-m-d', strtotime('+1 day', strtotime($date))),
            'id_card' => $patient->id_number,
            'hospital_code' => $patient->hospital->code,
//            'page'=>1,
            'limit' => self::PAGE_SIZE
        ];

        return $this->runPayLog($post);
    }

    function runPayLog($post, $page = 1)
    {
        $post['page'] = $page;
        $api = Options::findOne(['name' => 'api_url'])->value;
        $client = new Client();
        $data = [
            'body' => json_encode($post, JSON_UNESCAPED_UNICODE),
            'headers' => ['content-type' => 'application/json']
        ];
        $response = $client->post($api, $data);
        return Json::decode($response->getBody()->getContents());
    }
}