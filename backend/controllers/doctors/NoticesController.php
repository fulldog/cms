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
    /**
     * @auth
     * - item group=转诊平台 category=公告 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=公告 description-get=查看 sort=0 method=get  
     * - item group=转诊平台 category=公告 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=公告 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=公告 description-post=删除 sort=0 method=post  
     * @return array
     */
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
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorNotices::className(),
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

        ];
    }
}
