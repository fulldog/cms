<?php

namespace common\models\doctors;

use common\helpers\Util;
use Yii;

/**
 * This is the model class for table "{{%doctor_article}}".
 *
 * @property int $id
 * @property int $hospital_id
 * @property string $title
 * @property string $desc
 * @property string $keywords
 * @property string $content
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class DoctorArticle extends My
{

    /**
     * 需要截取的文章缩略图尺寸
     */
    public static $thumbSizes = [
        ["w"=>220, "h"=>150],//首页文章列表
        ["w"=>168, "h"=>112],//精选导读
        ["w"=>185, "h"=>110],//文章详情下边图片推荐
        ["w"=>125, "h"=>86],//热门推荐
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_article}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['hospital_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 100],
            [['desc', 'keywords', 'img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hospital_id' => 'Hospital ID',
            'title' => '标题',
            'desc' => '描述',
            'img' => '缩略图',
            'keywords' => '关键词',
            'content' => '内容',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function beforeSave($insert)
    {
        $insert = $this->getIsNewRecord();
        Util::handleModelSingleFileUpload($this, 'img', $insert, '@thumb', ['thumbSizes'=>self::$thumbSizes]);
        if ($insert) {
            $this->hospital_id = Yii::$app->user->identity->hospital_id;
            if (!$this->hospital_id){
                $this->addError('hospital_id','请使用医院端登陆添加新闻');
                return false;
            }
        }
        return parent::beforeSave($insert);
    }
}
