<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "course_password".
 *
 * @property int $id
 * @property int $pid 课表ID
 * @property string $password 密码
 * @property string $used_by 密码
 * @property int $status 是否使用
 * @property int $created_at
 * @property int $updated_at
 */
class CoursePassword extends \yii\db\ActiveRecord
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
        return 'course_password';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'password'], 'required'],
            [['pid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['password', 'used_by'], 'string', 'max' => 255],
            [['pid', 'password'], 'unique', 'targetAttribute' => ['pid', 'password']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '课程ID',
            'password' => '密码',
            'status' => '是否使用',
            'used_by' => '使用者',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getCourse(){
        return $this->hasOne(Course::className(),['id'=>'pid']);
    }
}
