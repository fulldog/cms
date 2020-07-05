<?php

namespace app\models;

use Yii;
use common\helpers\Util;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "course_child".
 *
 * @property int $id
 * @property int $pid 课程ID
 * @property string $title 标题
 * @property string $desc 描述
 * @property string $thumb 封面图
 * @property string $video
 * @property int $created_at
 * @property int $updated_at
 */
class CourseChild extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course_child';
    }

    /**
     * 需要截取的文章缩略图尺寸
     */
    public static $thumbSizes = [
        ["w" => 220, "h" => 150],//首页文章列表
        ["w" => 168, "h" => 112],//精选导读
        ["w" => 185, "h" => 110],//文章详情下边图片推荐
        ["w" => 125, "h" => 86],//热门推荐
    ];

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'title'], 'required'],
            [['pid', 'created_at', 'updated_at'], 'integer'],
            [['title', 'desc', 'thumb', 'video'], 'string', 'max' => 255],
            [['thumb'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
            [['video'], 'file', 'skipOnEmpty' => true, 'extensions' => 'mp4'],
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
            'title' => '标题',
            'desc' => '描述',
            'thumb' => '封面图',
            'video' => '视频地址',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        if ($this->thumb) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->thumb = $cdn->getCdnUrl($this->thumb);
        }
        if ($this->video) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->video = $cdn->getCdnUrl($this->video);
        }
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $insert = $this->getIsNewRecord();
        Util::handleModelSingleFileUpload($this, 'thumb', $insert, '@uploads', ['thumbSizes' => self::$thumbSizes]);
        Util::handleModelSingleFileUpload($this, 'video', $insert, '@uploads');

        if ($this->thumb) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->thumb = str_replace($cdn->host, '', $this->thumb);
        }
        if ($this->video) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->video = str_replace($cdn->host, '', $this->video);
        }
        return parent::beforeSave($insert);
    }

    public function getCourse(){
        return $this->hasOne(Course::className(),['id'=>'pid']);
    }
}
