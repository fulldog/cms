<?php

namespace backend\controllers;

use Yii;
use common\models\DoctorInfos;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
use yii\data\ActiveDataProvider;
/**
 * DoctorController implements the CRUD actions for DoctorInfos model.
 */
class DoctorController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    
                        $dataProvider = new ActiveDataProvider([
                            'query' => DoctorInfos::find(),
                        ]);

                        return [
                            'dataProvider' => $dataProvider,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => DoctorInfos::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => DoctorInfos::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DoctorInfos::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => DoctorInfos::className(),
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorInfos::className(),
            ],
        ];
    }
}
