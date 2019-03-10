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
    /**
     * @auth
     * - item group=转诊平台 category=佣金比例 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=佣金比例 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=佣金比例 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=佣金比例 description-post=删除 sort=0 method=post  
     * - item group=转诊平台 category=佣金比例 description-get=查看 sort=0 method=get  
     * @return array
     */
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
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorCommission::className(),
            ],
        ];
    }
}
