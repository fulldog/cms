<?php

namespace backend\controllers\doctors;

use Yii;
use common\models\doctors\DoctorInfosSearch;
use common\models\doctors\DoctorInfos;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
/**
 * DoctorsController implements the CRUD actions for DoctorInfos model.
 */
class DoctorsController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    
                        $searchModel = new DoctorInfosSearch();
                        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
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
