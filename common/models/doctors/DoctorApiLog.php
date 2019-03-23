<?php

namespace common\models\doctors;

use Yii;

/**
 * This is the model class for table "{{%doctor_api_log}}".
 *
 * @property int $id
 * @property int $patient_id
 * @property string $query_time
 * @property int $hospital_id
 * @property int $status
 * @property int $created_at
 */
class DoctorApiLog extends My
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_api_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['patient_id', 'query_time', 'hospital_id'], 'required'],
            [['patient_id', 'hospital_id', 'status', 'created_at'], 'integer'],
            [['query_time'], 'string', 'max' => 20],
            [['patient_id', 'hospital_id', 'query_time'], 'unique', 'targetAttribute' => ['patient_id', 'hospital_id', 'query_time']],
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
            'query_time' => 'Query Time',
            'hospital_id' => 'Hospital ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
