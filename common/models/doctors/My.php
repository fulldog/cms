<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2018/12/29
 * Time: 23:04
 */

namespace common\models\doctors;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

class My extends ActiveRecord
{
    public $pageSize = 20;

    public $_status = [
        '待审核', '通过', '拒绝',
    ];
    public $_recommendMap = [
        '否', '是'
    ];

    public $_transfer = [
        '否', '是'
    ];

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    function getStatus()
    {
        return $this->_status[$this->status];
    }

    function getTransfer()
    {
        return $this->_transfer[$this->transfer];
    }

    static function _getStatusAll($arg = 'status')
    {
        $map = [
            'status' => ['待审核', '通过', '拒绝'],
            'recommend' => ['否', '是'],
            'transfer' => ['否', '是'],
        ];
        return $map[$arg];
    }

    function getRecommend()
    {
        return $this->_recommendMap[$this->recommend];
    }

    function getStatus2()
    {
        return $this->_status[$this->status];
    }

    function getHospital()
    {
        return $this->hasOne(DoctorHospitals::className(), ['id' => 'hospital_id']);
    }

    function getPatient()
    {
        return $this->hasOne(DoctorPatients::className(), ['id' => 'patient_id']);
    }

    function getDoctor()
    {
        return $this->hasOne(DoctorInfos::className(), ['id' => 'doctor_id']);
    }

    function SearchAddHospitalId($query, $key = 'hospital_id')
    {
        if (\Yii::$app->user->identity->hospital_id) {
            $query->andFilterWhere([
                $key => \Yii::$app->user->identity->hospital_id,
            ]);
        } else {
            $query->andFilterWhere([
                $key => $this->$key,
            ]);
        }
        return $query;
    }

    function SearchAddTime($query, $params, $class_name)
    {
        $key = basename(str_replace('\\', '/', $class_name));
        if (isset($params[$key]['created_at']) && $params[$key]['created_at']) {
            $created_at = explode('~', $params[$key]['created_at']);
            $query->andFilterWhere(['between', 'created_at', strtotime($created_at[0]), strtotime($created_at[1])]);
        }
        if (isset($params[$key]['updated_at']) && $params[$key]['updated_at']) {
            $updated_at = explode('~', $params[$key]['updated_at']);
            $query->andFilterWhere(['between', 'updated_at', strtotime($updated_at[0]), strtotime($updated_at[1])]);
        }
        return $query;
    }
}