<?php

namespace backend\controllers;

use Yii;
use common\services\CourseCateServiceInterface;
use common\services\CourseCateService;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
use yii\data\ActiveDataProvider;
/**
 * CourseCateController implements the CRUD actions for CourseCate model.
 */
class CourseCateController extends \yii\web\Controller
{
    /**
    * @auth
    * - item group=未分类 category=Course Cates description-get=列表 sort=000 method=get
    * - item group=未分类 category=Course Cates description=创建 sort-get=001 sort-post=002 method=get,post  
    * - item group=未分类 category=Course Cates description=修改 sort=003 sort-post=004 method=get,post  
    * - item group=未分类 category=Course Cates description-post=删除 sort=005 method=post  
    * - item group=未分类 category=Course Cates description-post=排序 sort=006 method=post  
    * - item group=未分类 category=Course Cates description-get=查看 sort=007 method=get  
    * @return array
    */
    public function actions()
    {
        /** @var CourseCateServiceInterface $service */
        $service = Yii::$app->get(CourseCateServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function($query, $indexAction) use($service){
                    $result = $service->getList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                                            ];
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
