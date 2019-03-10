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

    /**
     * @auth
     * - item group=转诊平台 category=医院管理 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=医院管理 description-get=查看 sort=0 method=get  
     * - item group=转诊平台 category=医院管理 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=医院管理 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=医院管理 description-post=删除 sort=0 method=post  
     * @return array
     */
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
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorHospitals::className(),
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
        ];
    }

}
