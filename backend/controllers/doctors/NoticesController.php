<?php

namespace backend\controllers\doctors;

use Yii;
use common\models\doctors\DoctorNoticesSearch;
use common\models\doctors\DoctorNotices;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
/**
 * NoticesController implements the CRUD actions for DoctorNotices model.
 */
class NoticesController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    
                        $searchModel = new DoctorNoticesSearch();
                        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],
        ];
    }
}
