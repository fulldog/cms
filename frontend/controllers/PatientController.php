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

    const PAGE_SIZE = 20 * 100;
    const GET_PAY_DETAIL = "getPayDetail";

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

    function has_check($patient_id, $hospital_id, $date)
    {
        $prefix = Yii::$app->db->tablePrefix;
        $info = Yii::$app->db->createCommand("select status,id from " . $prefix . "doctor_api_log where patient_id=:patient_id and hospital_id=:hospital_id and query_time=:query_time", [
            ':hospital_id' => $hospital_id,
            ':patient_id' => $patient_id,
            ':query_time' => $date,
        ])->queryOne();

        if (!empty($info)){
            if ($info['status'] > 0){
                $start = strtotime($date);
                $end = $start + 1 * 24 * 3600 - 1;
                echo json_encode([
                    'data' => DoctorPaylog::find()
                        ->select(['hospital_id', 'patient_id', 'patient_name', 'id_card', 'money', 'pay_status_text', 'project', 'desc', 'created_at'])
                        ->where(['patient_id' => $patient_id, 'hospital_id' => $hospital_id])
                        ->andWhere(['between', 'created_at', $start, $end])
                        ->asArray()
                        ->all(),
                    'code' => 1
                ],JSON_UNESCAPED_UNICODE);
                exit();
            }else{
                $id = $info['id'];
            }
        }else{
            Yii::$app->db->createCommand()->insert($prefix . "doctor_api_log", [
                'hospital_id' => $hospital_id,
                'patient_id' => $patient_id,
                'query_time' => $date,
                'created_at' => time(),
            ])->execute();
            $id = Yii::$app->db->getLastInsertID();
        }

        return $id;
    }

    function actionPayLog($patient_id, $date)
    {
        $patient_id = intval($patient_id);
        $date = trim($date);
        set_time_limit(0);
        $patient = DoctorPatients::find()->where(['id' => $patient_id])->with('hospital')->one();

//        $logId = $this->has_check($patient_id, $patient->hospital->id, $date);

        $config = Yii::$app->params['hospital_api'][$patient->hospital->code];
        if (empty($config)) {
            return [
                'code' => 0,
                'msg' => '没有查到接口配置信息'
            ];
        }
        $api = $config['api_url'];
        $post = [
            'sign' => md5($patient->id_number . $patient->hospital->code),
            'start_time' => 0,
            'end_time' => date('Y-m-d', strtotime('+1 day', strtotime($date))),
            'id_card' => $patient->id_number,
            'hospital_code' => $patient->hospital->code,
            'limit' => self::PAGE_SIZE,
            'page' => 1,
            'method' => self::GET_PAY_DETAIL,
//            'start_time' => strtotime($date),
//            'end_time' => strtotime('+1 day', strtotime($date)) - 1,
        ];

        $client = new Client(['timeout' => 5]);
        try {
            $response = $client->post($api, ['form_params' => $post]);

            //todo 这里不做任何操作，直接把数据仍给前端
            exit($response->getBody()->getContents());

            //todo 一下是逐条处理提成  移到定时任务处理
            $res = \GuzzleHttp\json_decode(trim($response->getBody()->getContents(), "\xEF\xBB\xBF"), true);
            $lists = [];
            if ($res['code'] == 200 && !empty($res['data'])) {
                $lists[] = $res['data']['list'];
                $results = $this->yeild_curl(ceil($res['data']['count'] / self::PAGE_SIZE), function ($page) use ($client, $post, $api) {
                    $post['page'] = $page;
                    //规范rest接口使用
//                $data = [
//                    'body' => json_encode($post, JSON_UNESCAPED_UNICODE),
//                    'headers' => ['content-type' => 'application/json']
//                ];
                    $response = $client->post($api, ['form_params' => $post]);
                    return \GuzzleHttp\json_decode(trim($response->getBody()->getContents(), "\xEF\xBB\xBF"), true);
                });

                foreach ($results as $item) {
                    if ($item['code'] == 200 && !empty($item['data'])) {
                        $lists[] = $res['data']['list'];
                    }
                }
                if ($this->paylogSave($lists, $patient_id)) {
                    Yii::$app->db->createCommand()->update(Yii::$app->db->tablePrefix.'doctor_api_log',[
                        'status'=>1,
                    ],'id='.$logId);
                    return [
                        'msg' => '拉取并录入成功',
                        'code' => 1,
                        'data' => $lists
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
        } catch (\Exception $e) {
            return [
                'code' => 0,
                'msg' => '接口访问异常，请稍后再试'
            ];
        }
    }

    function yeild_curl($count, callable $callback)
    {
        for ($i = 2; $i <= $count; $i++) {
            yield $callback($i);
        }
    }

    function paylogSave($data, $patient_id)
    {
        if (Yii::$app->request->get('debug') == 'debug') {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }

        set_time_limit(0);
        $commit = true;
        $patient = DoctorPatients::find()->where(['id' => $patient_id])->with('hospital')->one();
        $commission = DoctorCommission::find()->where(['patient_id' => $patient_id])->orderBy(['id' => SORT_DESC])->one();
        if (empty($commission)) {
            $commission = DoctorCommission::find()->where(['hospital_id' => $patient->hospital->id])->orderBy(['id' => SORT_DESC])->one();
        }

        if (empty($commission)){
            exit(json_encode([
                'code' => 0,
                'msg' => '还有配置提成规则，请联系管理员:以下是流水数据',
                'data'=>$data
            ],JSON_UNESCAPED_UNICODE));
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
            'project',
            'desc',
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
                        $v['money'],
                        $v['pay_status_text'],
                        $v['project'],
                        $v['desc'],
                        strtotime($v['created_at']),
                        $v['updated_at'] ?? strtotime($v['updated_at']),
                    ];
                    $menoylog[] = [
                        $patient->hospital->id,
                        $patient->is_transfer ? $patient->transfer_doctor : $patient->doctor_id,
                        $patient_id,
                        'add',
                        $v['desc'],
                        round($v['money'] * $commission->point / 100, 2),
                        1,
                        time()
                    ];
                }
            }
            if (!empty($paylog) && !empty($menoylog)) {
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
            file_put_contents('paylog.log', var_export($data, true), FILE_APPEND|LOCK_EX);
//            Yii::info($data,'paylog');
        }
        return $commit;
    }
}