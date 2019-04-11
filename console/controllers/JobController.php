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
use common\models\doctors\DoctorPaylog;

set_time_limit(0);

class JobController extends Task
{

    const GET_DAY_FLOW = "getDayFlow";

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

    /**
     * api test
     */
    function actionTest(){
        $this->params = [
            'sign'=>md5('42272219520826124234eIPM74Tj'),
            'date'=>'2015-08-25',
            'id_card'=>'420583199408123467,422723195401170437',
        ];
        echo "test";
        print_r($this->params);
    }

    /**
     * get T-1 day pays
     */
    function actionIndex()
    {
        ini_set('memory_limit','1024M');
        ini_set('default_socket_timeout', 1);
        set_time_limit(0);
        try {
            $time1 = microtime(true);
            $this->stdout("job begin".PHP_EOL);
            $this->date = $this->getBeforeDay(1);
            $hospital = $this->getAllHospitals();
            foreach ($this->search($hospital) as $result) {
                if (!empty($result) && $result['code'] == 200) {
                    foreach ($result['data'] as $id_card => $info) {
                        $patient = $this->patients[$this->hid . '-' . $id_card];
                        unset($this->patients[$this->hid . '-' . $id_card]);
                        if (!empty($patient)) {
                            $commission = DoctorCommission::find()->where(['patient_id' => $patient->id])->orderBy(['id' => SORT_DESC])->one();
                            if (empty($commission)) {
                                $commission = DoctorCommission::find()->where(['hospital_id' => $this->hid])->orderBy(['id' => SORT_DESC])->one();
                            }
                            if (!empty($commission) && !empty($info)) {
                                $insert = [];
                                $insert2 = [];
                                foreach ($info as $k => $v) {
                                    if (empty($v))
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
                                        round($v['money'] * $commission->point / 100, 2),
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
                                    if ($commit) {
                                        $tran->commit();
                                    } else {
                                        $tran->rollBack();
                                        $this->logs['DoctorPatientDayMoney'] = $insert;
                                        $this->logs['DoctorMoneylog'] = $insert2;
                                        $this->stderr("commit fail:".json_encode($this->logs,JSON_UNESCAPED_UNICODE).PHP_EOL);
                                    }
                                }
                            } else {
                                $this->logs['DoctorCommission'] = ['还没有配置当前病人得提成率', $patient->toArray()];
                            }
                        }
                    }
                }
                $this->logs();
            }
            $time2 = microtime(true);
            $this->stdout('time used:'.(round($time2 - $time1,3)).'hs'.PHP_EOL);
            $this->stdout('memory_get_usage used:'.memory_get_usage().PHP_EOL);
            $this->stdout("job end".PHP_EOL);
        } catch (\Exception $exception) {
            $this->stderr("job error:".$exception->getMessage().PHP_EOL);
            return ;
        }
    }

    /**
     * 得到所有有转诊的医院
     * @return array|DoctorPatients[]
     */
    function getAllHospitals()
    {
        return DoctorPatients::find()->select(['hospital_id'])
            ->where(['is_transfer' => 1])
            ->distinct(['hospital_id'])
            ->all();
    }

    function getPatients($hospital_id)
    {
        return DoctorPatients::find()->select(['doctor_id','is_transfer','transfer_doctor','id_number','name','phone'])
            ->where(['hospital_id' => $hospital_id])
            ->andWhere(['is_transfer' => 1])
            ->groupBy('id_number')
            ->orderBy(['id'=>SORT_ASC])
            ->all();
    }

    /**
     * @param DoctorHospitals $hospitals
     * @return \Generator
     */
    function search($hospitals)
    {
        if (!empty($hospitals)) {
            foreach ($hospitals as $hospital) {
                $this->hid = $hospital->hospital_id;
                $hospital_detail = DoctorHospitals::findOne(['id'=>$this->hid]);
                $this->hostipal_code = $hospital_detail->code;
                $patients = $this->getPatients($this->hid);
                $this->logs['hospital_name'] = $hospital_detail->hospital_name;
                $this->logs['api_config'] = $config = \Yii::$app->params['hospital_api'][$hospital_detail->code];
                if (!empty($patients) && !empty($config['task_api'])) {
                    foreach ($patients as $patient) {
                        $this->stdout("current patient:".$patient->name.'--'.$patient->id_number.PHP_EOL);
                        $this->patients[$this->hid . '-' . $patient->id_number] = $patient;
                        $this->params['id_card'] = $patient->id_number;
                        $this->params['name'] = $patient->name;
                        $this->params['phone'] = $patient->phone;
                        $this->params['sign'] = md5($patient->id_number . $hospital->code);
                        $this->params['date_time'] = $this->date;
                        $this->params['method'] = self::GET_DAY_FLOW;
                        yield $this->curl($config['task_api']);
                    }
                }else{
                    $this->stdout("没有病人/没有查到接口配置信息hid:{$this->hid},code:{$hospital->code},name:{$hospital->hospital_name}".PHP_EOL);
                }
            }
        }
    }
}