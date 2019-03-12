<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;

use common\models\doctors\DoctorCommission;
use common\models\doctors\DoctorInfos;
use common\models\doctors\DoctorMoneylog;
use common\models\doctors\DoctorPatients;
use common\models\doctors\DoctorPaylog;
use common\models\Options;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\Json;
use yii\log\Logger;


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

        $doctor = DoctorInfos::findOne(['uid' => $this->uid]);
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
        if ($patient->is_transfer > 0) {
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
    {
        $doctor = DoctorInfos::findOne(['uid' => $this->uid]);
        return [
            'data' => DoctorPatients::find()->where(['doctor_id' => $doctor->id])
                ->orWhere(['transfer_doctor' => $doctor->id])
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
        $doctor = DoctorInfos::findOne(['uid' => $this->uid]);
        return [
            'data' => DoctorPatients::find()->where(['doctor_id' => $doctor->id, 'is_transfer' => 1])
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

    function actionPayLog2($patient_id, $date)
    {
        $patient = DoctorPatients::find()->where(['id' => $patient_id])->with('hospital')->one();
        $post = [
            'sign' => md5($patient->id_number . $patient->hospital->code),
//            'start_time' => strtotime($date),
//            'end_time' => strtotime('+1 day', strtotime($date)) - 1,
            'page' => 1,
            'start_time' => $date,
            'end_time' => date('Y-m-d', strtotime('+1 day', strtotime($date))),
            'id_card' => $patient->id_number,
            'hospital_code' => $patient->hospital->code,
            'limit' => self::PAGE_SIZE
        ];
        return $this->runPayLog($post, $patient);
    }

    function actionPayLog($patient_id, $date)
    {
        set_time_limit(0);
        $patient = DoctorPatients::find()->where(['id' => $patient_id])->with('hospital')->one();
        $post = [
            'sign' => md5($patient->id_number . $patient->hospital->code),
//            'start_time' => strtotime($date),
//            'end_time' => strtotime('+1 day', strtotime($date)) - 1,
            'page' => 1,
            'start_time' => $date,
            'end_time' => date('Y-m-d', strtotime('+1 day', strtotime($date))),
            'id_card' => $patient->id_number,
            'hospital_code' => $patient->hospital->code,
            'limit' => self::PAGE_SIZE
        ];
        $api = Options::findOne(['name' => 'api_url'])->value;
        $client = new Client();
        $data = [
            'body' => json_encode($post, JSON_UNESCAPED_UNICODE),
            'headers' => ['content-type' => 'application/json']
        ];
        $response = $client->post($api, $data);
        $res = json_decode($result = trim($response->getBody()->getContents(), "\xEF\xBB\xBF"), true);
        $lists = [];
        if ($res['code'] == 200) {
            $lists[] = $res['data'];
            $results = $this->yeild_curl(ceil($res['count'] / self::PAGE_SIZE), function ($page) use ($client, $post, $api) {
                $post['page'] = $page;
                $data = [
                    'body' => json_encode($post, JSON_UNESCAPED_UNICODE),
                    'headers' => ['content-type' => 'application/json']
                ];
                $response = $client->post($api, $data);
                return json_decode(trim($response->getBody()->getContents(), "\xEF\xBB\xBF"), true);
            });

            foreach ($results as $item) {
                if ($item['code'] == 200 && !empty($item['data'])) {
                    $lists[] = $item['data'];
                }
            }
            if ($this->paylogSave($lists, $patient_id)) {
                return [
                    'msg' => '拉取并录入成功',
                    'code' => 1
                ];
            } else {
                return [
                    'msg' => '拉取成功，录入失败',
                    'code' => 0
                ];
            }
        }
        return [
            'msg' => '拉取失败，请稍后再试',
            'code' => 0
        ];
    }

    function yeild_curl($count, callable $callback)
    {
        for ($i = 2; $i <= $count; $i++) {
            yield $callback($i);
        }
    }

    function paylogSave($data, $patient_id)
    {
        if (Yii::$app->request->get('debug')=='debug'){
           echo json_encode($data,JSON_UNESCAPED_UNICODE);die;
        }

        set_time_limit(0);
        $commit = true;
        $patient = DoctorPatients::find()->where(['id' => $patient_id])->with('hospital')->one();
        $commission = DoctorCommission::find()->where(['patient_id' => $patient_id])->orderBy(['id' => SORT_DESC])->one();
        if (empty($commission)) {
            $commission = DoctorCommission::find()->where(['hospital_id' => $patient->hospital->id])->orderBy(['id' => SORT_DESC])->one();
        }
        $paylog_keys = [
            'hospital_id',
            'hospital_name',
            'patient_id',
            'patient_name',
            'out_trade_no',
            'id_card',
            'money',
            'pay_status_text',
            'row_number',
            'project',
            'sdesc',
            'created_at',
            'updated_at'
        ];
        $menoylog_keys = [
            'hospital_id',
            'doctor_id',
            'patient_id',
            'type',
            'desc',
            'money',
            'status',
            'created_at',
        ];
        $db = Yii::$app->db;
        $tran = $db->beginTransaction();
        foreach ($data as $item) {
            $paylog = [];
            $menoylog = [];
            foreach ($item as $v) {
                if (!DoctorPaylog::findOne([
                    'out_trade_no' => $v['out_trade_no'],
//                        'id_card'=>$v['id_card'],
                    'hospital_id' => $patient->hospital->id,
                    'patient_id' => $patient_id,
                ])) {
                    $paylog[] = [
                        $patient->hospital->id,
                        $patient->hospital->hospital_name,
                        $patient_id,
                        $patient->name,
                        $v['out_trade_no'],
                        $patient->id_number,
                        $v['itemamou'],
                        $v['pay_status_text'],
                        $v['ROW_NUMBER'],
                        $v['project'],
                        $v['patient_name'] . "[{$v['id_card']}]" . $v['sdesc'],
                        strtotime($v['created_at']),
                        strtotime($v['updated_at']),
                    ];
                    $menoylog[] = [
                        $patient->hospital->id,
                        $patient->is_transfer ? $patient->transfer_doctor : $patient->doctor_id,
                        $patient_id,
                        'add',
                        $v['patient_name'] . "[{$v['id_card']}]" . $v['project'] . $v['itemamou'],
                        round($v['itemamou'] * $commission->point / 100, 2),
//                            Yii::$app->formatter->asScientific($v['itemamou'] * $commission->point / 100, 2),
                        1,
                        time()
                    ];
                }
            }
            if (!empty($paylog) && !empty($menoylog)){
                if (!$db->createCommand()->batchInsert(DoctorPaylog::tableName(), $paylog_keys, $paylog)->execute()
                    || !$db->createCommand()->batchInsert(DoctorMoneylog::tableName(), $menoylog_keys, $menoylog)->execute()
                ) {
                    $commit = false;
                }
            }
        }
        if ($commit) {
            $tran->commit();
        } else {
            $tran->rollBack();
            file_put_contents('paylog.log',var_export($data,true),FILE_APPEND);
//            Yii::info($data,'paylog');
        }
        return $commit;
    }
}