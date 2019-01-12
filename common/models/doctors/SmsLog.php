<?php

namespace common\models\doctors;

use Yii;

/**
 * This is the model class for table "{{%sms_log}}".
 *
 * @property int $id
 * @property string $phone
 * @property string $imgcode
 * @property string $code
 * @property int $created_at
 * @property int $updated_at
 */
class SmsLog extends My
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sms_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['imgcode', 'code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'imgcode' => 'Imgcode',
            'code' => 'Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
