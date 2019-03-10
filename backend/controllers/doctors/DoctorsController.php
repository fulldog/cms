<?php

namespace backend\controllers\doctors;

use common\models\doctors\DoctorHospitals;
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

    /**
     * @auth
     * - item group=转诊平台 category=医生信息 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=医生信息 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=医生信息 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=医生信息 description-post=删除 sort=0 method=post  
     * - item group=转诊平台 category=医生信息 description-get=查看 sort=0 method=get  
     * @return array
     */
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
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorInfos::className(),
            ],
        ];
    }
}
