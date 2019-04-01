<?php

namespace backend\controllers\doctors;

use Yii;
use common\models\doctors\DoctorArticleSearch;
use common\models\doctors\DoctorArticle;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
/**
 * ArticleController implements the CRUD actions for DoctorArticle model.
 */
class ArticleController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=转诊平台 category=医院动态 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=医院动态 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=医院动态 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=医院动态 description-post=删除 sort=0 method=post  
     * - item group=转诊平台 category=医院动态 description-get=查看 sort=0 method=get  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    
                        $searchModel = new DoctorArticleSearch();
                        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => DoctorArticle::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => DoctorArticle::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DoctorArticle::className(),
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorArticle::className(),
            ],
        ];
    }
}
