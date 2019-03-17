<?php

namespace common\models\doctors;

use Yii;

/**
 * This is the model class for table "{{%doctor_paylog}}".
 *
 * @property int $id
 * @property int $hospital_id
 * @property string $hospital_name
 * @property int $patient_id
 * @property string $patient_name
 * @property string $out_trade_no
 * @property string $id_card
 * @property string $money 金额
 * @property string $pay_status_text 状态
 * @property string $project
 * @property string $desc
 * @property int $created_at
 * @property int $updated_at
 */
class DoctorPaylog extends My
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_paylog}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hospital_id', 'hospital_name', 'patient_id', 'patient_name'], 'required'],
            [['hospital_id', 'patient_id', 'created_at', 'updated_at'], 'integer'],
            [['money'], 'number'],
            [['hospital_name', 'out_trade_no', 'project', 'desc'], 'string', 'max' => 100],
            [['patient_name'], 'string', 'max' => 50],
            [['id_card'], 'string', 'max' => 20],
            [['pay_status_text'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hospital_id' => 'Hospital ID',
            'hospital_name' => 'Hospital Name',
            'patient_id' => 'Patient ID',
            'patient_name' => 'Patient Name',
            'out_trade_no' => 'Out Trade No',
            'id_card' => 'Id Card',
            'money' => '金额',
            'pay_status_text' => '状态',
            'project' => 'Project',
            'desc' => 'desc',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
