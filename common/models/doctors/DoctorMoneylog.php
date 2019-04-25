<?php

namespace common\models\doctors;

use Yii;

/**
 * This is the model class for table "{{%doctor_moneylog}}".
 *
 * @property int $id
 * @property int $doctor_id
 * @property int $hospital_id
 * @property int $patient_id
 * @property string $type
 * @property string $desc
 * @property string $money
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 * @property int $out_key
 */
class DoctorMoneylog extends My
{

    public $typeText = [
        'add'=>'抽成',
        'reduce'=>'提现'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_moneylog}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_id', 'patient_id','hospital_id'], 'required'],
            [['doctor_id', 'patient_id', 'created_at', 'updated_at', 'status','hospital_id'], 'integer'],
            [['money'], 'number'],
            [['type'], 'string', 'max' => 10],
            [['desc','out_key'], 'string', 'max' => 255],
            [['money'],'compare','compareValue'=>0,'operator'=>'>='],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doctor_id' => 'Doctor ID',
            'patient_id' => 'Patient ID',
            'type' => '类型',
            'desc' => '描述',
            'money' => '金额',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'status' => '状态',
            'hospital_id' => '医院id',
            'out_key' => 'out_key',
        ];
    }

    function getType(){
        return $this->typeText[$this->type];
    }

    function getRelationPdmlog(){
        return $this->hasOne(DoctorPatientDayMoney::className(), ['out_key' => 'out_key']);
    }
}
