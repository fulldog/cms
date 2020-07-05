<?php

namespace app\models;

use backend\behaviors\TimeSearchBehavior;
use common\helpers\Util;
use Yii;

/**
 * This is the model class for table "vote_child".
 *
 * @property int $id
 * @property int $vid 投票ID
 * @property string $title 标题
 * @property string $desc 描述
 * @property int $pv 访问量
 * @property int $vote_count 票数
 * @property string $img 图片
 * @property int $created_at
 * @property int $updated_at
 */
class VoteChild extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote_child';
    }

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vid', 'title'], 'required'],
            [['vid', 'pv', 'vote_count', 'created_at', 'updated_at'], 'integer'],
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
            'vid' => '投票ID',
            'title' => '标题',
            'desc' => '描述',
            'pv' => '访问量',
            'vote_count' => '票数',
            'img' => '图片',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getVote(){
        return $this->hasOne(Vote::className(),['id'=>'vid']);
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
        return parent::beforeSave($insert);
    }
}
