<?php

namespace backend\controllers\doctors;

use backend\actions\ExportAction;
use common\models\doctors\DoctorCommission;
use common\models\doctors\DoctorHospitals;
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

    /**
     * @auth
     * - item group=转诊平台 category=资金结算 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=资金结算 description-get=查看 sort=0 method=get  
     * - item group=转诊平台 category=资金结算 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=资金结算 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=资金结算 description-post=删除 sort=0 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function () {
                    $searchModel = new DoctorMoneylogSearch();
                    $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorMoneylog::className(),
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
        ];
    }

    /**
     * @auth - item group=转诊平台 category=资金结算 description=同意提现 sort-get=0 method=get
     */
    function actionStatusSucc($id)
    {
        $model = DoctorMoneylog::findOne(['id' => $id]);
        $doctor = DoctorInfos::findOne(['id' => $model->doctor_id]);
        if (!empty($model) && !empty($doctor)) {
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
        } else {
            Yii::$app->getSession()->setFlash('error', '数据错误');
        }
    }

    /**
     * 单日或单月财务图表
     *
     * @auth - item group=转诊平台 category=资金结算 description=图表查看 sort-get=0 sort-post=0 method=get,post
     */
    function actionChart()
    {
        $data = Yii::$app->request->post('data');
        if (!Yii::$app->user->identity->hospital_id) {
            $hospital_id = $data['hospital_id'] ?? null;
        } else {
            $hospital_id = Yii::$app->user->identity->hospital_id;
        }
        $query = DoctorMoneylog::find()->select('sum(money) as money,type,status')->andFilterWhere([
            'hospital_id' => $hospital_id,
        ]);
        if ($data['time']) {
            $time = explode('~', $data['time']);
            $query->andFilterWhere(['between', 'created_at', strtotime($time[0]), strtotime($time[1])]);
        }
//        echo $query->createCommand()->getRawSql();
        $json['title'] = '全平台收益';
        if ($hospital_id) {
            $hospital = DoctorHospitals::findOne(['id' => $hospital_id]);
            $json['title'] = $hospital->hospital_name . '收益';
        }
        $res = $query->groupBy(['status', 'type'])->asArray()->all();
        if (!empty($res)) {
            foreach ($res as $item) {
                if ($item['status'] == 1 && $item['type'] == 'add') {
                    $json['add'] = $item['money'];
                } elseif ($item['status'] == 1 && $item['type'] == 'reduce') {
                    $json['reduce'] = $item['money'];
                } else {
                    $json['reduce_no'] = $item['money'];
                }
            }
        }
        return $this->render($this->action->id, [
            'hospital' => DoctorHospitals::find()->getHospitals(0, false, true),
            'post' => $data,
            'json' => $json,
        ]);
    }

    /**
     * @auth - item group=转诊平台 category=资金结算 description=导出表格 sort-get=0 method=get
     */
    function actionExport()
    {
//        $title = ['医院名称', '医生姓名', '病人名称', '病人身份证', '类型', '状态', '描述', '金额', '创建时间'];
        $title = ['医院名称', '医生姓名', '病人名称', '病人身份证', '金额类型', '金额', '比例', '提成金额', '转诊日期', 'HIS时间', '状态'];
        $get = \yii::$app->getRequest()->get('data');
        $query = DoctorMoneylog::find()
            ->with('hospital')->with('doctor')->with('patient')
            ->alias('m')
            ->select(['m.*', 'pdm.date']);
        $query->joinWith('relationPdmlog');
        $keyId = 'hospital_id';
        $inputName = 'DoctorMoneylogSearch';

        $ptypeMap = [
            'menzhen' => '门诊',
            'zhuyuan' => '住院',
        ];
        if ($get[$inputName]['date']) {
            $tmp = $get[$inputName]['date'];
            unset($get[$inputName]['date']);
            $tmp = explode('~', str_replace(' ', '', $tmp));
            $query->andFilterWhere(['between', 'pdm.date', $tmp[0], $tmp[1]]);
        }

        if (isset($get[$inputName])) {
            $query->andFilterWhere($get[$inputName]);
        }

        if (\Yii::$app->user->identity->hospital_id) {
            $query->andWhere([
                $keyId => \Yii::$app->user->identity->hospital_id,
            ]);
        }
//        echo $query->createCommand()->getRawSql();
        //提成比例
        $commissiong = DoctorCommission::find()->all();
        $commissiongArr = [];
        foreach ($commissiong as $c) {
            $commissiongArr[$c->hospital_id . $c->patient_id . $c->extend1] = $c->point;
        }
        $data = [];
        $insert = [];
        $lists = $query->all();
        foreach ($lists as $item) {
            if ($item->type == 'add') {
                if (isset($insert[$item->doctor->id])) {
                    $insert[$item->doctor->id]['money'] += $item->money;
                } else {
                    $insert[$item->doctor->id] = [
                        'money' => $item->money,
                        'hospital_id' => $item->hospital->id,
                    ];
                }
            }
            $data[] = [
                $item->hospital->hospital_name,
                $item->doctor->name,
                $item->patient->name,
                $item->patient->id_number . "\t",
                $ptypeMap[$item->relationPdmlog->type],
                $item->relationPdmlog->money,
                ($commissiongArr[$item->hospital->id . $item->patient->id . $item->relationPdmlog->type] ?? $commissiongArr[$item->hospital->id]) . '%',
                $item->money,
//                $item->getType(),
                Yii::$app->formatter->asDatetime($item->patient->created_at),
                $item->relationPdmlog->date,
                $item->getStatus(),
//                Yii::$app->formatter->asDatetime($item->created_at),
            ];
        }
//        if (!empty($insert)) {
//            $tran = Yii::$app->db->beginTransaction();
//            try {
//                foreach ($insert as $doctorId => $arr) {
//                    if (!DoctorInfos::updateAllCounters(['money' => -$arr['money']], "id={$doctorId} and money>={$arr['money']}")) {
//                        throw new \Exception("削减医生金额失败/金额不足：doctor_id={$doctorId} and money>={$arr['money']}");
//                    }
//                    Yii::$app->db->createCommand()->insert(DoctorMoneylog::tableName(), [
//                        'doctor_id' => $doctorId,
//                        'hospital_id' => $arr['hospital_id'],
//                        'type' => 'reduce',
//                        'money' => $arr['money'],
//                        'status' => 1,
//                        'desc' => '提现',
//                        'out_key' => md5($doctorId . $arr['hospital_id'] . $arr['money'] . uniqid()),
//                        'created_at' => time(),
//                    ])->execute();
//                }
//                $tran->commit();
//                echo '自动结算提现成功';
//                if (!empty($data)) {
//                    exportToExcel($title, $data);
//                }
//            } catch (\Exception $e) {
//                $tran->rollBack();
//                echo '自动结算提现失败' . $e->getMessage();
//                exit();
//            }
//        }
        exportToExcel($title, $data);
    }
}
