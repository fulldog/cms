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
    public  $_status = [
        '待审核', '通过', '拒绝' ,
    ];

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    function getStatus($returnArr=false){
        if ($returnArr){
            return [$this->status=>$this->_status[$this->status]];
        }
        return $this->_status[$this->status];
    }

    static function _getStatusAll(){
       return [
           '待审核', '通过', '拒绝' ,
       ];
    }
}