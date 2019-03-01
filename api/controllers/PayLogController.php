<?php
namespace api\controllers;

use yii\web\Response;

class PayLogController extends \yii\rest\ActiveController
{
    public $modelClass = "api\models\PayLog";

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;//默认浏览器打开返回json
        return $behaviors;
    }

    public function actions()
    {
        return [];
    }

    public function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
        ];
    }
    /**
     * @return array
     */
    function actionIndex(){
        return [
            'code'=>200,
            'error'=>'',
            'data'=>[
                'count'=>200,
                'current_page'=>1,
                'pageSize'=>20,
                'list'=>[
                    [
                        'out_trade_no'=>'',//	是	string	外部单号
                        'patient_name'=>'',//	否	string	消费主体
                        'id_card'=>'',//	是	string	身份证
                        'project'=>'',//	是	string	流水项目
                        'desc'=>'',//	否	string	描述
                        'money'=>'',//	是	float	精确到百分位
                        'pay_status_text'=>'',//	是	string	账单状态描述
                        'created_at'=>'',//	是	string	创建时间
                        'updated_at'=>'',//	否	string	修改时间
                    ],
                ]
            ]
        ];
    }

}
