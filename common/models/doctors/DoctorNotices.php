<?php

namespace common\models\doctors;

use Yii;

/**
 * This is the model class for table "{{%doctor_notices}}".
 *
 * @property int $id
 * @property int $hospital_id
 * @property string $notice
 * @property int $status
 * @property string $to
 * @property int $created_at
 * @property int $updated_at
 */
class DoctorNotices extends My
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_notices}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hospital_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['notice'], 'required'],
            [['notice'], 'string', 'max' => 255],
            [['to'], 'string', 'max' => 10],
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
            'notice' => '公告内容',
            'status' => '状态',
            'to' => '对象',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
