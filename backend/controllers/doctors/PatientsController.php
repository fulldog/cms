<?php

namespace backend\controllers\doctors;

use Yii;
use common\models\doctors\DoctorPatientsSearch;
use common\models\doctors\DoctorPatients;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
/**
 * PatientsController implements the CRUD actions for DoctorPatients model.
 */
class PatientsController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    
                        $searchModel = new DoctorPatientsSearch();
                        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => DoctorPatients::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => DoctorPatients::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DoctorPatients::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => DoctorPatients::className(),
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorPatients::className(),
            ],
        ];
    }
}
