<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;


use common\models\doctors\DoctorMoneylog;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;

class MyController extends BaseController
{

    function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                ],
            ],
//            'access' => [
//                'only' => [],
//            ]
        ]);
    }

    function actionWallet(){
        $query = DoctorMoneylog::find()->select('sum(money) as money,type,status')->andFilterWhere([
            'doctor_id' =>$this->getDoctor()->id,
        ]);
        $res = $query->groupBy(['status','type'])->asArray()->all();
        $json = [];
        if (!empty($res)){
            foreach ($res as $item){
                if ($item['status']==1 && $item['type']=='add'){
                    $json['add'] = $item['money'];
                }elseif ($item['status']==1 && $item['type']=='reduce'){
                    $json['reduce'] = $item['money'];
                }else{
                    $json['reduce_no'] = $item['money'];
                }
            }
        }
        return [
            'code'=>1,
            'msg'=>'add/收入；reduce/已提现；reduce_no/提现中',
            'data'=>$json
        ];
    }

    function actionGetme(){
        return ArrayHelper::merge(['is_complete'=>$this->getDoctor()],Yii::$app->user->identity);
    }

}