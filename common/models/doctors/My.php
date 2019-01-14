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
use yii\db\Exception;

class My extends ActiveRecord
{
    public $pageSize = 20;

    public  $_status = [
        '待审核', '通过', '拒绝' ,
    ];
    public $_recommendMap = [
        '否','是'
    ];

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    function getStatus(){
        return $this->_status[$this->status];
    }

    static function _getStatusAll($arg='status'){
        $map = [
            'status'=>['待审核', '通过', '拒绝'],
            'recommend'=>['否','是']
        ];
       return $map[$arg];
    }

    function getRecommend(){
        return $this->_recommendMap[$this->recommend];
    }
}