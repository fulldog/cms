<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vote_record".
 *
 * @property int $id
 * @property int $vid 投票活动ID
 * @property int $vcid 投票ID
 * @property int $uid 用户ID
 * @property string $date
 * @property int $created_at
 * @property int $updated_at
 */
class VoteRecord extends \yii\db\ActiveRecord
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
        return 'vote_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vid', 'vcid', 'uid', 'date'], 'required'],
            [['vid', 'vcid', 'uid', 'created_at', 'updated_at'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vid' => '投票活动ID',
            'vcid' => '投票ID',
            'uid' => '用户ID',
            'date' => 'Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
