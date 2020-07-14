<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "course_cate".
 *
 * @property int $id
 * @property string $name 分类名称
 * @property string $alias_name 别名
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class CourseCate extends \yii\db\ActiveRecord
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
        return 'course_cate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'alias_name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '分类名称',
            'alias_name' => '图片',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function getAllCates()
    {
        $data = [];
        $all = CourseCate::find()->asArray()->all();
        foreach ($all as $item) {
            $data[$item['id']] = $item['name'];
        }
        return $data;
    }
}
