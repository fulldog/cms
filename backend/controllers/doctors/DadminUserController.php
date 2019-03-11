<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/15
 * Time: 0:21
 */

namespace backend\controllers\doctors;


use backend\controllers\AdminUserController;
use backend\models\DadminUser;
use backend\models\search\DadminUserSearch;
use Yii;
use backend\models\User;
use backend\models\search\UserSearch;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
use yii\web\Response;

class DadminUserController extends AdminUserController
{
    protected $name_fix = 'hospital_admin';

    private $hospital_admin_role = '医院管理员';
    /**
     * @auth
     *
     * - item group=转诊平台 category=管理员 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=管理员 description-get=查看 sort=0 method=get  
     * - item group=转诊平台 category=管理员 description-post=删除 sort=0 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var UserSearch $searchModel */
                    $searchModel = Yii::createObject( DadminUserSearch::className() );
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DadminUser::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DadminUser::className(),
            ],
        ];
    }

    /**
     * 创建管理员账号
     *
     * @auth - item group=转诊平台 category=管理员 description=创建 sort-get=0 sort-post=0 method=get,post
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var User $model */
        $model = Yii::createObject( DadminUser::className() );
        $model->setScenario('create');
        if (Yii::$app->getRequest()->getIsPost()) {
            $data = Yii::$app->getRequest()->post();
            if (empty($data["DadminUser"]['hospital_id'])){
                $data["DadminUser"]['hospital_id'] = 0;
            }
            if ( $model->load($data) && $model->save() && $model->assignPermission() ) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                return $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model->loadDefaultValues();
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 创建医院管理员
     *
     * @auth - item group=转诊平台 category=管理员 description=创建(医院列表) sort-get=0 method=get
     *
     * @param $hospital_id
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    function actionHospital($hospital_id){
        $model = Yii::createObject( DadminUser::className() );
        $model->setScenario('create');
        $model->username = $this->name_fix.$hospital_id;
        $model->hospital_id = $hospital_id;
        $model->password = '123456';
        $model->permissions = [];

        if ($model->save() && $model->assignPermission() ) {
            Yii::$app->db->createCommand()->insert('auth_assignment', [
                'item_name' => $this->hospital_admin_role,
                'user_id' => $model->id,
                'created_at' => time(),
            ])->execute();
            Yii::$app->getSession()->setFlash('success', '创建成功');
        } else {
            Yii::$app->getSession()->setFlash('error', $model->getFirstError());
        }
        return true;
    }

    /**
     * 修改管理员账号
     *
     * @auth - item group=转诊平台 category=管理员 description=修改 sort-get=0 sort-post=0 method=get,post
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws \Throwable
     */
    public function actionUpdate($id)
    {
        $model = DadminUser::findOne($id);
        $model->setScenario('update');
        $model->roles = $model->permissions = call_user_func(function() use($id){
            $permissions = Yii::$app->getAuthManager()->getAssignments($id);
            foreach ($permissions as $k => &$v){
                $v = $k;
            }
            return $permissions;
        });
        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->request->post()) && $model->save() && $model->assignPermission() ) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Success'));
                return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
//            $model = User::findOne(['id' => Yii::$app->getUser()->getIdentity()->getId()]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
}