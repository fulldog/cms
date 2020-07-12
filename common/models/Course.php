<?php

namespace common\models;

use common\helpers\Util;
use common\models\Category;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "course".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $desc 描述
 * @property string $wechat_img 微信群图片
 * @property string $thumb 介绍图片
 * @property string $banner 介绍图片
 * @property string $video 介绍视频
 * @property int $status 状态
 * @property int $cid 分类
 * @property int $price 价格
 * @property int $recommend 是否推荐
 * @property int $tags 标签
 * @property int $created_at
 * @property int $updated_at
 */
class Course extends \yii\db\ActiveRecord
{
    /**
     * 需要截取的文章缩略图尺寸
     */
    public static $thumbSizes = [
//        ["w" => 220, "h" => 150],//首页文章列表
//        ["w" => 168, "h" => 112],//精选导读
//        ["w" => 185, "h" => 110],//文章详情下边图片推荐
//        ["w" => 125, "h" => 86],//热门推荐
    ];

    public $userCount;
    public $childCount;

    public static $_tags = [
        'good' => '精品课程',
        'free' => '免费课程',
        'hot' => '热门课程'
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
    public static function tableName()
    {
        return 'course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'cid', 'price','tags'], 'required'],
            [['status', 'recommend', 'created_at', 'updated_at', 'price', 'cid'], 'integer'],
            [['title', 'desc', 'tags'], 'string', 'max' => 255],
            [['thumb', 'wechat_img','banner'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
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
            'title' => '标题',
            'cid' => '分类',
            'desc' => '描述',
            'tags' => '标签',
            'banner' => '推荐图',
            'wechat_img' => '微信群图片',
            'thumb' => '介绍图片',
            'video' => '介绍视频',
            'status' => '状态',
            'recommend' => '是否推荐',
            'price' => '价格(元)',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
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
        if ($this->banner) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->banner = $cdn->getCdnUrl($this->banner);
        }
        if ($this->wechat_img) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->wechat_img = $cdn->getCdnUrl($this->wechat_img);
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
        Util::handleModelSingleFileUpload($this, 'wechat_img', $insert, '@uploads', ['thumbSizes' => self::$thumbSizes]);
        Util::handleModelSingleFileUpload($this, 'video', $insert, '@uploads');
        Util::handleModelSingleFileUpload($this, 'banner', $insert, '@uploads');

        if ($this->thumb) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->thumb = str_replace($cdn->host, '', $this->thumb);
        }
        if ($this->wechat_img) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->wechat_img = str_replace($cdn->host, '', $this->wechat_img);
        }
        if ($this->banner) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->banner = str_replace($cdn->host, '', $this->banner);
        }
        if ($this->video) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->video = str_replace($cdn->host, '', $this->video);
        }
        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        if (CourseChild::findOne(['course_id' => $this->id]) != null) {
            $this->addError('id', "请先删除课时");
            return false;
        }
        if (CoursePassword::findOne(['course_id' => $this->id]) != null) {
            $this->addError('id', "请先删除课时密码");
            return false;
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CourseCate::className(), ['id' => 'cid']);
    }

    public function getChild()
    {
        return $this->hasMany(CourseChild::className(), ['course_id' => 'id']);
    }
}
