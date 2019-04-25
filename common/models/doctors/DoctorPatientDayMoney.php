<?php

namespace common\models\doctors;

use Yii;

/**
 * This is the model class for table "{{%doctor_patient_day_money}}".
 *
 * @property int $id
 * @property int $patient_id
 * @property string $type
 * @property string $desc
 * @property string $money
 * @property int $hospital_name
 * @property string $id_card
 * @property string $name
 * @property string $date
 * @property int $created_at
 * @property int $updated_at
 * @property int $out_key
 */
class DoctorPatientDayMoney extends My
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_patient_day_money}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['patient_id', 'type'], 'required'],
            [['patient_id', 'hospital_name', 'created_at', 'updated_at'], 'integer'],
            [['money'], 'number'],
            [['type'], 'string', 'max' => 20],
            [['desc', 'id_card', 'name', 'date','out_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'patient_id' => 'Patient ID',
            'type' => '类型',
            'desc' => '描述',
            'money' => '金额',
            'hospital_name' => '医院名称',
            'id_card' => '身份证',
            'name' => '姓名',
            'date' => '结算日期',
            'created_at' => '创建日期',
            'updated_at' => '修改日期',
            'out_key' => 'out_key',
        ];
    }
}
