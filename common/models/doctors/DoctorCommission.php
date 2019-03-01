<?php

namespace common\models\doctors;

use Yii;

/**
 * This is the model class for table "{{%doctor_commission}}".
 *
 * @property int $id
 * @property int $hospital_id 医院
 * @property int $patient_id 病人
 * @property int $point 比列
 * @property string $extend1
 * @property string $extend2
 * @property string $extend3
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class DoctorCommission extends My
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_commission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hospital_id',], 'required'],
            [['point'],'compare','compareValue'=>0,'operator'=>'>='],
            [['point'],'compare','compareValue'=>100,'operator'=>'<='],
            [['hospital_id', 'patient_id', 'point', 'created_at', 'updated_at'], 'integer'],
            [['extend2', 'extend3'], 'string', 'max' => 255],
            [['extend1'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hospital_id' => '医院',
            'patient_id' => '病人',
            'point' => '百分比',
            'extend1' => '身份证',
            'extend2' => 'Extend2',
            'extend3' => 'Extend3',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

}
