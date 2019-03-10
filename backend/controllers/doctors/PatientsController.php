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

    /**
     * @auth
     * - item group=转诊平台 category=病人管理 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=病人管理 description-get=查看 sort=0 method=get  
     * - item group=转诊平台 category=病人管理 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=病人管理 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=病人管理 description-post=删除 sort=0 method=post  
     * @return array
     */
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
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorPatients::className(),
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
        ];
    }
}
