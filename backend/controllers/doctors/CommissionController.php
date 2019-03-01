<?php

namespace backend\controllers\doctors;

use Yii;
use common\models\doctors\CommissionSearch;
use common\models\doctors\DoctorCommission;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
/**
 * CommissionController implements the CRUD actions for DoctorCommission model.
 */
class CommissionController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    
                        $searchModel = new CommissionSearch();
                        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => DoctorCommission::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => DoctorCommission::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DoctorCommission::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => DoctorCommission::className(),
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorCommission::className(),
            ],
        ];
    }
}
