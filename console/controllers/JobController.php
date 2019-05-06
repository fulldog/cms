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
use common\models\doctors\DoctorInfos;
use common\models\doctors\DoctorMoneylog;
use common\models\doctors\DoctorPatientDayMoney;
use common\models\doctors\DoctorPatients;

class JobController extends Task
{

    const GET_DAY_FLOW = "getDayFlow";

    const GET_DAY_FLOW_LIST = 'getDayFlowList';
    const GET_DAY_FLOW_LIST_FAIL = 'getDayFlowListFail';

    const JOB_IS_DOING = 'job-is-doing';

    private $hid;
    private $date;
    protected $patient;

    public $day_log_keys = [
        'patient_id',
        'type',
        'desc',
        'money',
        'hospital_name',
        'id_card',
        'name',
        'created_at',
        'date',
        'out_key'
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
        'out_key'
    ];

    function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
        ini_set('memory_limit', '1024M');
        ini_set('default_socket_timeout', 1);
        set_time_limit(0);
    }

    /**
     * test
     * @param null $date
     * @throws \yii\db\Exception
     */
    function actionTest($date = null)
    {
        $res = $this->getPatients(145);
        $this->date = $date ?? $this->getBeforeDay(-1);

        foreach ($res as $v) {
            echo uniqid().PHP_EOL;
            continue;
            $this->actionPatientDay($v->id, $this->date);
        }
    }

    /**
     * 手动添加数据
     * @param string $s 开始时间
     * @param string $e 结束时间
     * @param int $hospital_id
     * @param int $out_hospital_id 排除在外的id
     * @return void
     */
    function actionAddToRedis($s, $e, $hospital_id = null, $out_hospital_id = null)
    {
        $hospitals = $this->getAllHospitals($hospital_id);
        if ($out_hospital_id) {
            $out_hospital_id = explode(',', $out_hospital_id);
        }else{
            $out_hospital_id = [];
        }
        if (!empty($hospitals)) {
            $cache = \Yii::$app->redis;
            $cache->select(6);
            if ($s && $e) {
                $days = (strtotime($e) - strtotime($s)) / 24 / 3600;
                $string = "起{$s}止{$e}日期相差天{$days}" . PHP_EOL;
                foreach ($hospitals as $hospital) {
                    if (!in_array($hospital->hospital_id, $out_hospital_id)) {
                        for ($i = 0; $i <= $days; $i++) {
                            $value = $hospital->hospital_id . ':' . $this->getBeforeDay($i, $s);
                            $string .= "成功添加任务：hospital_id：date|{$value}" . PHP_EOL;
                            $cache->rpush(self::GET_DAY_FLOW_LIST, $value);
                        }
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
     * 单独拉某个人的指定时间的数据
     * @param $pid
     * @param $date
     * @throws \yii\db\Exception
     */
    function actionPatientDay($pid, $date)
    {
        $this->date = $date;
        $this->patient = DoctorPatients::find()->select(['doctor_id', 'is_transfer', 'transfer_doctor', 'id_number', 'name', 'phone', 'id', 'hospital_id'])
            ->where(['id' => $pid])
            ->andWhere(['is_transfer' => 1])
            ->andWhere(['>', 'transfer_doctor', 0])
            ->orderBy(['id' => SORT_ASC])
            ->groupBy('id_number')
            ->one();

        if (!empty($this->patient) && !empty($this->date) && !empty($this->patient->hospital)) {

            if (!isset(\Yii::$app->params['hospital_api'][$this->patient->hospital->code]['task_api'])) {
                $this->stdout("没有查到接口配置信息hid:{$this->hid},code:{$this->patient->hospital->code},name:{$this->patient->hospital->hospital_name}" . PHP_EOL);
                return;
            }
            $this->hostipal_name = $this->patient->hospital->hospital_name;
            $this->stdout("current patient:" . $this->hostipal_name . $this->patient->name . '-' . $this->patient->id_number . '-' . $this->date . PHP_EOL);
            $this->hid = $this->patient->hospital->id;
            $this->logs['api_config'] = \Yii::$app->params['hospital_api'][$this->patient->hospital->code];
            $this->params['id_card'] = $this->patient->id_number;
            $this->params['name'] = $this->patient->name;
            $this->params['phone'] = $this->patient->phone;
            $this->params['sign'] = md5($this->patient->id_number . $this->patient->hospital->code);
            $this->params['date'] = $this->date;
            $this->params['method'] = self::GET_DAY_FLOW;
            $this->resultDo($this->curl($this->logs['api_config']['task_api']));
            $this->stdout(json_encode($this->logs, JSON_UNESCAPED_UNICODE) . PHP_EOL);
            $this->logs();
        }
    }


    /**
     * 后台计划任务
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
     * 后台计划任务  拉取前一天的his数据
     * get T-1 day pays
     */
    function actionDoCron()
    {
        try {
            $time1 = microtime(true);
            if (\Yii::$app->redis->get(self::JOB_IS_DOING)) {
                return $this->stdout("job is doing" . PHP_EOL);
            }
            \Yii::$app->redis->setnx(self::JOB_IS_DOING, 1);
            $this->stdout("job begin" . PHP_EOL);
            foreach ($this->searchRedis() as $result) {
                $this->resultDo($result);
                $this->stdout(json_encode($this->logs, JSON_UNESCAPED_UNICODE) . PHP_EOL);
                $this->logs();
            }
            $time2 = microtime(true);
            $this->stdout('time used:' . (round($time2 - $time1, 3)) . 'hs' . PHP_EOL);
            $this->stdout('memory_get_usage used:' . memory_get_usage() . PHP_EOL);
            $this->stdout("job end" . PHP_EOL);
            \Yii::$app->redis->del(self::JOB_IS_DOING);
        } catch (\Exception $exception) {
            $this->stdout("job error:" . $exception->getMessage() . PHP_EOL);
            \Yii::$app->redis->del(self::JOB_IS_DOING);
            $this->hasException = true;
            $this->logs();
        }
    }

    /**
     * 得到所有有转诊的医院
     * @param $hospital_id
     * @return array|DoctorPatients[]
     */
    function getAllHospitals($hospital_id = null)
    {
        return DoctorPatients::find()->select(['hospital_id'])
            ->where(['is_transfer' => 1])
            ->andFilterWhere(['=', 'hospital_id', $hospital_id])
            ->distinct(['hospital_id'])
            ->all();
    }

    /**
     * @param $hospital_id
     * @return array|DoctorPatients[]
     */
    function getPatients($hospital_id)
    {
        return DoctorPatients::find()->select(['doctor_id', 'is_transfer', 'transfer_doctor', 'id_number', 'name', 'phone', 'id'])
            ->where(['hospital_id' => $hospital_id])
            ->andWhere(['is_transfer' => 1])
            ->andWhere(['>', 'transfer_doctor', 0])
            ->orderBy(['id' => SORT_ASC])
            ->groupBy('id_number')
            ->all();
    }

    /**
     *
     * 2019年4月23日22:54:32
     * @return \Generator
     */
    function searchRedis()
    {
        $redis = \Yii::$app->redis;
        if ($redis->exists(self::GET_DAY_FLOW_LIST)) {
            while ($string = $redis->lpop(self::GET_DAY_FLOW_LIST)) {
                $this->hid = $this->patient = $this->date = $this->hostipal_code = '';
                $this->params = $this->logs = [];
                $this->stdout('START***************************' . $string . '********************************START' . PHP_EOL);
                $data = explode(':', $string);
                try {
                    if (empty($data)) {
                        throw new \Exception("data数据错误" . PHP_EOL);
                    }
                    $this->hid = $data[0];
                    $this->date = $data[1];
                    $hospital_detail = DoctorHospitals::findOne(['id' => $this->hid]);
                    if (!isset(\Yii::$app->params['hospital_api'][$hospital_detail->code]['task_api'])) {
                        throw new \Exception("没有查到接口配置信息-hospital_id:{$this->hid},code:{$hospital_detail->code},name:{$hospital_detail->hospital_name}" . PHP_EOL);
                    }
                    $patients = $this->getPatients($this->hid);
                    if (empty($patients)) {
                        throw new \Exception("没有满足条件的数据-hospital_id:{$this->hid},code:{$hospital_detail->code},name:{$hospital_detail->hospital_name}" . PHP_EOL);
                    }
                    $this->hostipal_code = $hospital_detail->code;
                    $this->logs['hospital_name'] = $this->hostipal_name = $hospital_detail->hospital_name;
                    $this->logs['api_config'] = \Yii::$app->params['hospital_api'][$hospital_detail->code];
                    foreach ($patients as $patient) {
                        $this->stdout("current patient:" . $this->hostipal_name . $patient->name . '--' . $patient->id_number . PHP_EOL);
                        $this->patient = $patient;
                        $this->params['id_card'] = $patient->id_number;
                        $this->params['name'] = $patient->name;
                        $this->params['phone'] = $patient->phone;
                        $this->params['sign'] = md5($patient->id_number . $hospital_detail->code);
                        $this->params['date'] = $this->date;
                        $this->params['method'] = self::GET_DAY_FLOW;
                        yield $this->curl($this->logs['api_config']['task_api']);
                    }
                } catch (\Exception $exception) {
                    $this->hasException = true;
                    $this->logs['searchRedis_Exception'] = $exception->getMessage();
                    $this->stdout($this->logs['searchRedis_Exception']);
                    yield [];
                }
                $this->stdout('END*****************************' . $string . '**********************************END' . PHP_EOL);
                $this->stdout('' . PHP_EOL);
            }
        }
    }

    /**
     * 结果集处理
     * @param $result
     * @throws \yii\db\Exception
     */
    function resultDo($result)
    {
        if (!empty($result) && $result['code'] == 200) {
            $db = \Yii::$app->db;
            foreach ($result['data'] as $id_card => $info) {
                $patient = $this->patient;
                if (!empty($patient)) {
                    $commission = DoctorCommission::find()->where(['patient_id' => $patient->id])->orderBy(['id' => SORT_DESC])->asArray()->one();
                    if (empty($commission)) {
                        $commission = DoctorCommission::find()->where(['hospital_id' => $this->hid])->orderBy(['id' => SORT_DESC])->asArray()->one();
                    }
                    if (!empty($commission) && !empty($info)) {
                        $this->logs['DoctorCommission'] = $commission;
                        $insert = [];
                        $insert2 = [];
                        $_money = 0;
                        foreach ($info as $k => $v) {
                            //容错
                            if (empty($v['money'])) {
                                continue;
                            }
                            $v['money'] = floatval($v['money']);
                            $rate = round($v['money'] * $commission['point'] / 100, 2);
                            $out_key = md5($this->date . $patient->id . $id_card . $patient->transfer_doctor . $v['money'] . $k . uniqid());
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
                                $out_key
                            ];

                            $insert2[] = [
                                $this->hid,
                                $patient->transfer_doctor,
                                $patient->id,
                                'add',
                                $v['desc'],
                                $rate,
                                1,
                                time(),
                                $out_key
                            ];
                            $_money += $rate;
                        }

                        if (!empty($insert) && !empty($insert2)) {
                            $this->logs['DoctorPatientDayMoney'] = $insert;
                            $this->logs['DoctorMoneylog'] = $insert2;
                            $tran = $db->beginTransaction();
                            if (!$db->createCommand()->batchInsert(DoctorPatientDayMoney::tableName(), $this->day_log_keys, $insert)->execute()
                                || !$db->createCommand()->batchInsert(DoctorMoneylog::tableName(), $this->menoylog_keys, $insert2)->execute()
                                || !DoctorInfos::updateAllCounters(['money' => $_money], ['id' => $patient->transfer_doctor])
                            ) {
                                $tran->rollBack();
                                $this->hasException = true;
                                $this->logs['rollBack'] = '-------------------------------Commit:Fail------------------------------------';
                            } else {
                                $tran->commit();
                                $this->logs['commit'] = '-----------------------------Commit:Success----------------------------------';
                            }
                        }
                    } else {
                        $this->logs['DoctorCommission'] = '还没有配置当前病人提成率';
                    }
                }
            }
        }
    }
}
