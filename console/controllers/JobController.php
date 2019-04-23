<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/3/22
 * Time: 23:56
 */

namespace console\controllers;


use common\models\doctors\DoctorCommission;
use common\models\doctors\DoctorHospitals;
use common\models\doctors\DoctorMoneylog;
use common\models\doctors\DoctorPatientDayMoney;
use common\models\doctors\DoctorPatients;

class JobController extends Task
{

    const GET_DAY_FLOW = "getDayFlow";

    const GET_DAY_FLOW_LIST = 'getDayFlowList';

    private $hid;
    private $patients = [];
    private $date;

    public $day_log_keys = [
        'patient_id',
        'type',
        'desc',
        'money',
        'hospital_name',
        'id_card',
        'name',
        'created_at',
        'date'
    ];

    public $menoylog_keys = [
        'hospital_id',
        'doctor_id',
        'patient_id',
        'type',
        'desc',
        'money',
        'status',
        'created_at',
    ];

    function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
        ini_set('memory_limit', '1024M');
        ini_set('default_socket_timeout', 1);
        set_time_limit(0);
    }

    /**
     * @param $date
     * api test
     */
    function actionTest($date)
    {
        echo $date . PHP_EOL;
        $this->params = [
            'sign' => md5('42272219520826124234eIPM74Tj'),
            'date' => $date,//'2019-04-18',
            'id_card' => '420500194611141325',
        ];
//        $this->curl('//58.19.245.66:9090/?can=xdtj');
//        print_r($this->logs);
    }

    /**
     * 手动添加数据
     * @param $s
     * @param $e
     * @param int $hid
     */
    function actionAddToRedis($s, $e, $hid = null)
    {
        $hospitals = $this->getAllHospitals($hid);
        if (!empty($hospitals)) {
            $cache = \Yii::$app->redis;
            $cache->select(6);
            if ($s && $e) {
                $days = (strtotime($e) - strtotime($s)) / 24 / 3600;
                $string = "起{$s}止{$e}日期相差天{$days}" . PHP_EOL;
                foreach ($hospitals as $hospital) {
                    for ($i = 0; $i <= $days; $i++) {
                        $value = $hospital->hospital_id . ':' . $this->getBeforeDay($i, $s);
                        $string .= "成功添加任务：hospital_id：date|{$value}" . PHP_EOL;
                        $cache->rpush(self::GET_DAY_FLOW_LIST, $value);
                    }
                }
                $this->stdout($string);
                $this->redisLogs($string);
            } else {
                $this->stdout('起止日期必须填写');
            }
        } else {
            $this->stderr("起{$s}止{$e}日期,没有数据");
        }

    }


    /**
     * 计划任务
     */
    function actionAutoRedis()
    {
        $date = $this->getBeforeDay(-1);
        $hospitals = $this->getAllHospitals();
        if (!empty($hospitals)) {
            $cache = \Yii::$app->redis;
            $cache->select(6);
            $string = "起{$date}止{$date}日期" . PHP_EOL;
            foreach ($hospitals as $hospital) {
                $value = $hospital->hospital_id . ':' . $date;
                $string .= "成功添加任务：hospital_id：date|{$value}" . PHP_EOL;
                $cache->rpush(self::GET_DAY_FLOW_LIST, $value);
            }
            $this->stdout($string);
            $this->redisLogs($string);
        } else {
            $this->stderr("处理日期：{$date}--今日没有数据");
        }
    }

    /**
     * get T-1 day pays
     */
    function actionIndex()
    {
        try {
            $time1 = microtime(true);
            $this->stdout("job begin" . PHP_EOL);
//            $this->date = $this->getBeforeDay(-1);
//            $hospital = $this->getAllHospitals();
//            foreach ($this->search($hospital) as $result) {
            foreach ($this->searchRedis() as $result) {
                if (!empty($result) && $result['code'] == 200) {
                    foreach ($result['data'] as $id_card => $info) {
                        $patient = $this->patients[$this->hid . '-' . $id_card];
                        unset($this->patients[$this->hid . '-' . $id_card]);
                        if (!empty($patient)) {
                            $commission = DoctorCommission::find()->where(['patient_id' => $patient->id])->orderBy(['id' => SORT_DESC])->asArray()->one();
                            if (empty($commission)) {
                                $commission = DoctorCommission::find()->where(['hospital_id' => $this->hid])->orderBy(['id' => SORT_DESC])->asArray()->one();
                            }
                            if (!empty($commission) && !empty($info)) {
                                $this->logs['DoctorCommission'] = $commission;
                                $insert = [];
                                $insert2 = [];
                                foreach ($info as $k => $v) {
                                    //容错
                                    if (empty($v['money']))
                                        continue;

                                    $insert[] = [
                                        $patient->id,
                                        $k,
                                        $v['desc'],
                                        $v['money'],
                                        $v['hospital_name'],
                                        $id_card,
                                        $v['name'],
                                        time(),
                                        $this->date,
                                    ];
                                    $insert2[] = [
                                        $this->hid,
                                        $patient->transfer_doctor,
                                        $patient->id,
                                        'add',
                                        $v['desc'],
                                        round($v['money'] * $commission['point'] / 100, 2),
                                        1,
                                        time()
                                    ];
                                }
                                if (!empty($insert) && !empty($insert2)) {
                                    $db = \Yii::$app->db;
                                    $tran = $db->beginTransaction();
                                    $commit = true;
                                    if (!$db->createCommand()->batchInsert(DoctorPatientDayMoney::tableName(), $this->day_log_keys, $insert)->execute()
                                        || !$db->createCommand()->batchInsert(DoctorMoneylog::tableName(), $this->menoylog_keys, $insert2)->execute()
                                    ) {
                                        $commit = false;
                                    }

                                    $this->logs['DoctorPatientDayMoney'] = $insert;
                                    $this->logs['DoctorMoneylog'] = $insert2;
                                    if ($commit) {
//                                        $tran->commit();
                                        $this->logs['commit'] = '----Commit:Success----';
                                    } else {
                                        $tran->rollBack();
                                        $this->logs['commit'] = '----Commit:Fail----';
                                    }
                                }
                            } else {
                                $this->logs['DoctorCommission'] = ['还没有配置当前病人提成率', $patient->toArray()];
                            }
                        }
                    }
                }
                $this->stdout(json_encode($this->logs, JSON_UNESCAPED_UNICODE) . PHP_EOL);
                $this->logs();
            }
            $time2 = microtime(true);
            $this->stdout('time used:' . (round($time2 - $time1, 3)) . 'hs' . PHP_EOL);
            $this->stdout('memory_get_usage used:' . memory_get_usage() . PHP_EOL);
            $this->stdout("job end" . PHP_EOL);
        } catch (\Exception $exception) {
            $this->stdout("job error:" . $exception->getMessage() . PHP_EOL);
            return;
        }
    }

    /**
     * 得到所有有转诊的医院
     * @param $hid
     * @return array|DoctorPatients[]
     */
    function getAllHospitals($hid = null)
    {
        return DoctorPatients::find()->select(['hospital_id'])
            ->where(['is_transfer' => 1])
            ->andFilterWhere(['=', 'hospital_id', $hid])
            ->distinct(['hospital_id'])
            ->all();
    }

    function getPatients($hospital_id)
    {
        return DoctorPatients::find()->select(['doctor_id', 'is_transfer', 'transfer_doctor', 'id_number', 'name', 'phone', 'id'])
            ->where(['hospital_id' => $hospital_id])
            ->andWhere(['is_transfer' => 1])
            ->groupBy('id_number')
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }

    /**
     * @param  DoctorPatients $hospital_ids
     * @return \Generator
     */
    function search($hospital_ids)
    {
        if (!empty($hospital_ids)) {
            foreach ($hospital_ids as $hid) {
                $this->hid = $hid->hospital_id;
                $hospital_detail = DoctorHospitals::findOne(['id' => $this->hid]);
                $this->hostipal_code = $hospital_detail->code;
                $patients = $this->getPatients($this->hid);
                $this->logs['hospital_name'] = $hospital_detail->hospital_name;
                try {
                    $this->logs['api_config'] = $config = \Yii::$app->params['hospital_api'][$hospital_detail->code];
                    if (!empty($patients) && !empty($config['task_api'])) {
                        foreach ($patients as $patient) {
                            $this->stdout("current patient:" . $patient->name . '--' . $patient->id_number . PHP_EOL);
                            $this->patients[$this->hid . '-' . $patient->id_number] = $patient;
                            $this->params['id_card'] = $patient->id_number;
                            $this->params['name'] = $patient->name;
                            $this->params['phone'] = $patient->phone;
                            $this->params['sign'] = md5($patient->id_number . $hospital_detail->code);
                            $this->params['date_time'] = $this->date;
                            $this->params['method'] = self::GET_DAY_FLOW;
                            yield $this->curl($config['task_api']);
                        }
                    }
                } catch (\Exception $exception) {
                    $this->stdout("没有病人/没有查到接口配置信息hid:{$this->hid},code:{$hospital_detail->code},name:{$hospital_detail->hospital_name}" . PHP_EOL);
                }
            }
        }
    }

    /**
     * 2019年4月23日22:54:32
     * @return \Generator
     */
    function searchRedis()
    {
        $redis = \Yii::$app->redis;
        if ($redis->exists(self::GET_DAY_FLOW_LIST)) {
            while ($string = $redis->lpop(self::GET_DAY_FLOW_LIST)) {
                $this->stdout('START******************************' . $string . '*********************************START' . PHP_EOL);
                $data = explode(':', $string);
                if (!empty($data)) {
                    $this->hid = $data[0];
                    $this->date = $data[1];
                    $hospital_detail = DoctorHospitals::findOne(['id' => $this->hid]);
                    $this->hostipal_code = $hospital_detail->code;
                    $this->logs['hospital_name'] = $this->hostipal_name = $hospital_detail->hospital_name;
                    $patients = $this->getPatients($this->hid);
                    try {

                        if (empty($patients) || !isset(\Yii::$app->params['hospital_api'][$hospital_detail->code]['task_api'])) {
                            throw new \Exception("没有病人/没有查到接口配置信息hid:{$this->hid},code:{$hospital_detail->code},name:{$hospital_detail->hospital_name}" . PHP_EOL);
                        }
                        $this->logs['api_config'] = $config = \Yii::$app->params['hospital_api'][$hospital_detail->code];
                        foreach ($patients as $patient) {
                            $this->stdout("current patient:" . $patient->name . '--' . $patient->id_number . PHP_EOL);
                            $this->patients[$this->hid . '-' . $patient->id_number] = $patient;
                            $this->params['id_card'] = $patient->id_number;
                            $this->params['name'] = $patient->name;
                            $this->params['phone'] = $patient->phone;
                            $this->params['sign'] = md5($patient->id_number . $hospital_detail->code);
                            $this->params['date_time'] = $this->date;
                            $this->params['method'] = self::GET_DAY_FLOW;
                            yield $this->curl($config['task_api']);
                        }
                    } catch (\Exception $exception) {
                        $this->stdout($exception->getMessage());
                        yield [];
                    }
                }
                $this->stdout('END*****************************' . $string . '**********************************END' . PHP_EOL);
            }
        }
    }
}