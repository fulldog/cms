<?php

namespace app\models;

use common\helpers\Util;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vote".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $desc 描述
 * @property int $start_time 开始时间
 * @property int $end_time 结束时间
 * @property string $img 图片
 * @property int $vote_count 累计投票
 * @property int $pv 访问量
 * @property int $updated_at
 * @property int $created_at
 */
class Vote extends \yii\db\ActiveRecord
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
        return 'vote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'start_time', 'end_time'], 'required'],
            [['vote_count', 'pv', 'updated_at', 'created_at'], 'integer'],
            [['title', 'desc', 'img'], 'string', 'max' => 255],
            [['img'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '活动标题',
            'desc' => '活动描述',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'img' => '图片',
            'vote_count' => '累计投票',
            'pv' => '访问量',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
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
        $this->start_time = date('Y-m-d H:i:s',$this->start_time);
        $this->end_time = date('Y-m-d H:i:s',$this->end_time);
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $insert = $this->getIsNewRecord();
        Util::handleModelSingleFileUpload($this, 'img', $insert, '@uploads');

        if ($this->img) {
            /** @var TargetAbstract $cdn */
            $cdn = Yii::$app->get('cdn');
            $this->img = str_replace($cdn->host, '', $this->img);
        }
        $this->start_time = strtotime($this->start_time);
        $this->end_time = strtotime($this->end_time);
        return parent::beforeSave($insert);
    }
}
