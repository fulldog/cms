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
 * @property string $money
 * @property int $status
 * @property string $extend1
 * @property string $extend2
 * @property string $extend3
 * @property int $created_at
 * @property int $updated_at
 */
class DoctorPaylog extends \common\models\doctors\My
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
            [['hospital_id', 'patient_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['money'], 'number'],
            [['hospital_name', 'patient_name', 'extend1', 'extend2', 'extend3'], 'string', 'max' => 255],
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
            'money' => '金额',
            'status' => 'Status',
            'id_number' => '身份证',
            'extend2' => 'Extend2',
            'extend3' => 'Extend3',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
