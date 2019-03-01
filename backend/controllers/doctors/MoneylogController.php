<?php

namespace backend\controllers\doctors;

use backend\actions\ExportAction;
use common\models\doctors\DoctorInfos;
use Yii;
use common\models\doctors\DoctorMoneylogSearch;
use common\models\doctors\DoctorMoneylog;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
/**
 * MoneylogController implements the CRUD actions for DoctorMoneylog model.
 */
class MoneylogController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                        $searchModel = new DoctorMoneylogSearch();
                        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => DoctorMoneylog::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => DoctorMoneylog::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DoctorMoneylog::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => DoctorMoneylog::className(),
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorMoneylog::className(),
            ],
        ];
    }

    function actionStatusSucc($id){
        $model = DoctorMoneylog::findOne(['id'=>$id]);
        $doctor = DoctorInfos::findOne(['id'=>$model->doctor_id]);
        if (!empty($model) && !empty($doctor)){
            $tran = Yii::$app->db->beginTransaction();
            $model->status = 1;
            $doctor->money = $doctor->money - $model->money;
            if ($model->save() && $doctor->save()) {
                $tran->commit();
                Yii::$app->getSession()->setFlash('success', '成功');
            } else {
                $tran->rollBack();
                Yii::$app->getSession()->setFlash('error', $model->getFirstError());
            }
        }else{
            Yii::$app->getSession()->setFlash('error', '数据错误');
        }
    }

    /**
     * 单日或单月财务图表
     * @param string $s
     */
    function actionEchart($s='day'){

    }

    function actionExport(){
        $title = ['医院名称','医生姓名','病人名称','病人身份证','类型','状态','描述','金额','创建时间'];
        $data = [];

        $get = \yii::$app->getRequest()->get('data');
        $query = DoctorMoneylog::find();
        $keyId = 'hospital_id';
        $inputName = 'DoctorMoneylogSearch';
        if (isset($get[$inputName])){
            $query->andFilterWhere($get[$inputName]);
        }

        if (\Yii::$app->user->identity->hospital_id){
            $query->andWhere([
                $keyId => \Yii::$app->user->identity->hospital_id,
            ]);
        }

        $lists = $query->all();
        foreach ($lists as $item){
            $data[] = [
                $item->hospital->hospital_name,
                $item->doctor->name,
                $item->patient->name,
                $item->patient->id_number."\t",
                $item->getType(),
                $item->getStatus(),
                $item->desc,
                $item->money,
                Yii::$app->formatter->asDatetime($item->created_at),
            ];
        }
        return exportToExcel($title,$data);
    }
}
