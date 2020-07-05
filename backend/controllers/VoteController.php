<?php

namespace backend\controllers;

use Yii;
use common\services\VoteServiceInterface;
use common\services\VoteService;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
/**
 * VoteController implements the CRUD actions for Vote model.
 */
class VoteController extends \yii\web\Controller
{
    /**
    * @auth
    * - item group=未分类 category=Votes description-get=列表 sort=000 method=get
    * - item group=未分类 category=Votes description=创建 sort-get=001 sort-post=002 method=get,post  
    * - item group=未分类 category=Votes description=修改 sort=003 sort-post=004 method=get,post  
    * - item group=未分类 category=Votes description-post=删除 sort=005 method=post  
    * - item group=未分类 category=Votes description-post=排序 sort=006 method=post  
    * - item group=未分类 category=Votes description-get=查看 sort=007 method=get  
    * @return array
    */
    public function actions()
    {
        /** @var VoteServiceInterface $service */
        $service = Yii::$app->get(VoteServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function($query, $indexAction) use($service){
                    $result = $service->getList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],                    ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'doCreate' => function($postData, $createAction) use($service){
                    return $service->create($postData);
                },
                'data' => function($createResultModel, $createAction) use($service){
                    $model = $createResultModel === null ? $service->newModel() : $createResultModel;
                    return [
                        'model' => $model,
                    ];
                }
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'doUpdate' => function($id, $postData, $updateAction) use($service){
                    return $service->update($id, $postData);
                },
                'data' => function($id, $updateResultModel, $updateAction) use($service){
                    $model = $updateResultModel === null ? $service->getDetail($id) : $updateResultModel;
                    return [
                        'model' => $model,
                    ];
                }
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'doDelete' => function($id, $deleteAction) use($service){
                    return $service->delete($id);
                },
            ],
            'sort' => [
                'class' => SortAction::className(),
                'doSort' => function($id, $sort, $sortAction) use($service){
                    return $service->sort($id, $sort);
                },
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'data' => function($id, $viewAction) use($service){
                    return [
                        'model' => $service->getDetail($id),
                    ];
                },
            ],
        ];
    }
}
