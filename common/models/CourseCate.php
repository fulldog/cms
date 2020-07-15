<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\helpers\Util;

/**
 * This is the model class for table "course_cate".
 *
 * @property int $id
 * @property string $name 分类名称
 * @property string $alias_name 别名
 * @property string $img img
 * @property string $img_chose 选择
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
            [['name','img','img_chose'], 'required'],
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
            'alias_name' => '别名',
            'img' => '分类图片',
            'img_chose' => '分类选中图片',
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

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        if ($this->img) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->img = $cdn->getCdnUrl($this->img);
        }
        if ($this->img_chose) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->img_chose = $cdn->getCdnUrl($this->img_chose);
        }
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $insert = $this->getIsNewRecord();
        Util::handleModelSingleFileUpload($this, 'img', $insert, '@uploads');
        Util::handleModelSingleFileUpload($this, 'img_chose', $insert, '@uploads');

        if ($this->img_chose) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->img_chose = str_replace($cdn->host, '', $this->img_chose);
        }
        if ($this->img) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->img = str_replace($cdn->host, '', $this->img);
        }
        return parent::beforeSave($insert);
    }
}
