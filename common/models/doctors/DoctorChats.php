<?php

namespace common\models\doctors;

use Yii;

/**
 * This is the model class for table "{{%doctor_chats}}".
 *
 * @property int $id
 * @property string $from 离线发送方
 * @property string $to 离线接收方
 * @property string $content 发送的离线内容
 * @property int $type 1 医生-医院2 医院-医院3 医院-管理
 * @property int $status 发送状态：0-未发送,1-已发送
 * @property int $created_at 发送方发送时间
 * @property int $updated_at
 */
class DoctorChats extends My
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_chats}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['from', 'to'], 'string', 'max' => 50],
            [['content'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => '发送方',
            'to' => '接收方',
            'content' => '内容',
            'type' => '消息类型',//1 医生-医院 2 医院-医院  3-admin
            'status' => '发送状态：0-未发送,1-已发送',
            'created_at' => '发送方发送时间',
            'updated_at' => 'Updated At',
        ];
    }

}
