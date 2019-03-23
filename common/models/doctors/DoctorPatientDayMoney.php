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
            [['desc', 'id_card', 'name', 'date'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'desc' => 'Desc',
            'money' => 'Money',
            'hospital_name' => 'Hospital Name',
            'id_card' => 'Id Card',
            'name' => 'Name',
            'date' => 'Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
