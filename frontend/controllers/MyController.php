<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 12:15
 */

namespace frontend\controllers;


use backend\models\User;
use common\models\doctors\DoctorHospitals;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

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
        ]);
    }

}