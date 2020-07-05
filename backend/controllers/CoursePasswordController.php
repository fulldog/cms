<?php

namespace backend\controllers;

use app\models\Course;
use app\models\CoursePassword;
use Yii;
use common\services\CoursePasswordServiceInterface;
use common\services\CoursePasswordService;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

/**
 * CoursePasswordController implements the CRUD actions for CoursePassword model.
 */
class CoursePasswordController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=未分类 category=Course Passwords description-get=列表 sort=000 method=get
     * - item group=未分类 category=Course Passwords description=创建 sort-get=001 sort-post=002 method=get,post  
     * - item group=未分类 category=Course Passwords description=修改 sort=003 sort-post=004 method=get,post  
     * - item group=未分类 category=Course Passwords description-post=删除 sort=005 method=post  
     * - item group=未分类 category=Course Passwords description-post=排序 sort=006 method=post  
     * - item group=未分类 category=Course Passwords description-get=查看 sort=007 method=get  
     * @return array
     */
    public function actions()
    {
        /** @var CoursePasswordServiceInterface $service */
        $service = Yii::$app->get(CoursePasswordServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function ($query, $indexAction) use ($service) {
                    $query['pid'] = Yii::$app->request->get('pid');
                    $result = $service->getList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'parent' => Course::findOne(['id' => $query['pid']]),
                    ];
                }
            ],
//            'create' => [
//                'class' => CreateAction::className(),
//                'doCreate' => function ($postData, $createAction) use ($service) {
//                    return $service->create($postData);
//                },
//                'data' => function ($createResultModel, $createAction) use ($service) {
//                    $model = $createResultModel === null ? $service->newModel() : $createResultModel;
//                    return [
//                        'model' => $model,
//                        'parent' => Course::findOne(['id' => Yii::$app->request->get('pid')]),
//                    ];
//                }
//            ],
            'update' => [
                'class' => UpdateAction::className(),
                'doUpdate' => function ($id, $postData, $updateAction) use ($service) {
                    return $service->update($id, $postData);
                },
                'data' => function ($id, $updateResultModel, $updateAction) use ($service) {
                    $model = $updateResultModel === null ? $service->getDetail($id) : $updateResultModel;
                    return [
                        'model' => $model,
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

    public function actionCreate()
    {
        $pid = Yii::$app->request->get('pid');
        if ($pid) {
            $pwd = uniqid() . $this->makePassword();
            while (CoursePassword::findOne(['password' => $pwd])) {
                $pwd = uniqid() . $this->makePassword();
            }
            $model = new CoursePassword();
            $model->password = $pwd;
            $model->pid = $pid;
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', '创建成功:');
            } else {
                Yii::$app->getSession()->setFlash('error', '创建失败，请重试');
            }
        } else {
            Yii::$app->getSession()->setFlash('error', '缺少pid参数');
        }
        return $this->redirect(['course-password/index', 'pid' => $pid]);
    }

    public static function makePassword($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        // 在 $chars 中随机取 $length 个数组元素键名
        $keys = array_rand($chars, $length);
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            // 将 $length 个数组元素连接成字符串
            $password .= $chars[$keys[$i]];
        }
        return $password;
    }
}
