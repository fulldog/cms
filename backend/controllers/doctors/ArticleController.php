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
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => DoctorArticle::className(),
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorArticle::className(),
            ],
        ];
    }
}
