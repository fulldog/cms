<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 10:02
 */

namespace backend\controllers\doctors;

use backend\actions\ViewAction;
use backend\controllers\UserController;
use backend\models\Duser;
use backend\models\search\DuserSearch;
use Yii;
use frontend\models\search\UserSearch;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

class DuserController extends UserController
{

    /**
     * @auth
     * - item group=转诊平台 category=医生账号 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=医生账号 description-get=查看 sort=0 method=get  
     * - item group=转诊平台 category=医生账号 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=医生账号 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=医生账号 description-post=删除 sort=0 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var UserSearch $searchModel */
                    $searchModel = Yii::createObject(DuserSearch::className());
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => Duser::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Duser::className(),
                'scenario' => 'create',
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Duser::className(),
                'scenario' => 'update',
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Duser::className(),
            ],
//            'sort' => [
//                'class' => SortAction::className(),
//                'modelClass' => Duser::className(),
//            ],
        ];
    }
}