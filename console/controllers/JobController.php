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
        try {
            $time = time();
            echo "job begin".PHP_EOL;
            $this->date = $this->getBeforeDay(1);
            $hos = $this->getAllHospitals();
            foreach ($this->search($hos) as $result) {
                if (!empty($result) && $result['code'] == 200) {
                    foreach ($result['data'] as $id_card => $info) {
                        $patient = $this->patients[$this->hid . '-' . $id_card];
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
                                    }
                                }
                            } else {
                                $this->logs['DoctorCommission'] = ['还没有配置当前病人得提成率', $patient->toArray()];
                            }
                        }
                    }
                }
            }
            $this->logs();
            echo 'time used:'.(time()-$time).PHP_EOL;
            echo "job end".PHP_EOL;
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            return ;
        }
    }

    function getAllHospitals($code = null)
    {
        $query = DoctorHospitals::find()->select(['hospital_name','id','code']);
        if ($code) {
            $query->where(['code' => $code]);
        }
        return $query->all();
    }

    function getPatients($hid)
    {
        return DoctorPatients::find()->select(['doctor_id','is_transfer','transfer_doctor','id_number','name'])
            ->where(['hospital_id' => $hid])
            ->andWhere(['is_transfer' => 1])
            ->groupBy('id_number')
            ->orderBy(['id'=>SORT_ASC])
            ->all();
    }

    /**
     * @param DoctorHospitals $hos
     * @return \Generator
     */
    function search($hos)
    {
        if (!empty($hos)) {
            foreach ($hos as $item) {
                $this->hid = $item->id;
                $this->hostipal_code = $item->code;
                $patient = $this->getPatients($this->hid);
                if (!empty($patient)) {
                    $config = \Yii::$app->params['hospital_api'][$item->code];
                    if (!empty($config)) {
                        foreach ($patient as $p) {
                            $this->patients[$this->hid . '-' . $p->id_number] = $p;
                            $this->params['id_card'] = $p->id_number;
                            $this->params['sign'] = md5($p->id_number . $item->code);
                            $this->params['date_time'] = $this->date;
                            $this->params['method'] = self::GET_DAY_FLOW;
                            yield $this->curl($config['api_url']);
                        }
                    } else {
                        echo "没有查到接口配置信息hid:{$this->hid},code:{$item->code}".PHP_EOL;
                        $this->logs(['msg' => '没有查到接口配置信息']);
                    }
                }
            }
        }
    }
}