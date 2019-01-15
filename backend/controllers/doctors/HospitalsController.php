<?php

namespace backend\controllers\doctors;

use backend\models\DadminUser;
use Yii;
use common\models\doctors\DoctorHospitalsSearch;
use common\models\doctors\DoctorHospitals;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
/**
 * HospitalsController implements the CRUD actions for DoctorHospitals model.
 */
class HospitalsController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    
                        $searchModel = new DoctorHospitalsSearch();
                        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => DoctorHospitals::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => DoctorHospitals::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DoctorHospitals::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => DoctorHospitals::className(),
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorHospitals::className(),
            ],
        ];
    }

}
