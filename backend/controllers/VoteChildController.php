<?php

namespace backend\controllers;

use app\models\Vote;
use Yii;
use common\services\VoteChildServiceInterface;
use common\services\VoteChildService;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;

/**
 * VoteChildController implements the CRUD actions for VoteChild model.
 */
class VoteChildController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=未分类 category=Vote Children description-get=列表 sort=000 method=get
     * - item group=未分类 category=Vote Children description=创建 sort-get=001 sort-post=002 method=get,post  
     * - item group=未分类 category=Vote Children description=修改 sort=003 sort-post=004 method=get,post  
     * - item group=未分类 category=Vote Children description-post=删除 sort=005 method=post  
     * - item group=未分类 category=Vote Children description-post=排序 sort=006 method=post  
     * - item group=未分类 category=Vote Children description-get=查看 sort=007 method=get  
     * @return array
     */
    public function actions()
    {
        /** @var VoteChildServiceInterface $service */
        $service = Yii::$app->get(VoteChildServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function ($query, $indexAction) use ($service) {
                    $query['VoteChildSearch']['vid'] = Yii::$app->request->get('VoteChildSearch')['vid'];
                    $result = $service->getList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],
                        'parent' => Vote::findOne(['id' => $query['VoteChildSearch']['vid']]),
                    ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'doCreate' => function ($postData, $createAction) use ($service) {
                    return $service->create($postData);
                },
                'data' => function ($createResultModel, $createAction) use ($service) {
                    $model = $createResultModel === null ? $service->newModel() : $createResultModel;
                    return [
                        'model' => $model,
                        'parent' => Vote::findOne(['id' => Yii::$app->request->get('VoteChildSearch')['vid']]),
                    ];
                }
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'doUpdate' => function ($id, $postData, $updateAction) use ($service) {
                    return $service->update($id, $postData);
                },
                'data' => function ($id, $updateResultModel, $updateAction) use ($service) {
                    $model = $updateResultModel === null ? $service->getDetail($id) : $updateResultModel;
                    return [
                        'model' => $model,
                        'parent' => Vote::findOne(['id' => Yii::$app->request->get('VoteChildSearch')['vid']]),
                    ];
                }
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'doDelete' => function ($id, $deleteAction) use ($service) {
                    return $service->delete($id);
                },
            ],
            'sort' => [
                'class' => SortAction::className(),
                'doSort' => function ($id, $sort, $sortAction) use ($service) {
                    return $service->sort($id, $sort);
                },
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'data' => function ($id, $viewAction) use ($service) {
                    return [
                        'model' => $service->getDetail($id),
                    ];
                },
            ],
        ];
    }
}