<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2018/12/29
 * Time: 23:04
 */

namespace common\models\doctors;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class My extends ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}