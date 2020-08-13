<?php

namespace common\models;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "course_password".
 *
 * @property int $id
 * @property int $course_id 课表ID
 * @property string $user_id 使用者ID
 * @property int $created_at
 * @property int $updated_at
 */
class UserCollect extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_collect';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['course_id', 'user_id'], 'required'],
            [['course_id', 'created_at', 'updated_at', 'user_id'], 'integer'],
//            [['course_id'], 'unique', 'targetAttribute' => ['course_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course_id' => '课程ID',
            'user_id' => '使用者',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
