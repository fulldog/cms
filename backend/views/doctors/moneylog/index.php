<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\doctors\DoctorMoneylogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收益查看';
$this->params['breadcrumbs'][] = yii::t('app_doctor', 'Doctor Moneylog');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget(['template'=>" {refresh} {export}"]) ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
//                        ['class' => CheckboxColumn::className()],
//                        'id',
//                        'doctor_id',
//                        [
//                            'attribute'=>'hospital.hospital_name'
//                        ],
                        [
                            'attribute'=>'doctor.name'
                        ],
                        [
                            'attribute'=>'patient.name',
                            'label'=>'病人名称'
                        ],
                        [
                            'attribute'=>'patient.id_number',
                            'label'=>'病人身份证'
                        ],
//                        'patient_id',
                        [
                            'attribute'=>'type',
                            'filter'=>[
                                'add'=>'抽成',
                                'reduce'=>'提现'
                            ],
                            'value'=>function($model){
                                return $model->getType();
                            }
                        ],
                        [
                            'attribute'=>'status',
                            'filter'=>[
                                '0'=>'未处理',
                                '1'=>'已通过'
                            ],
                            'format'=>'raw',
                            'value'=>function($model){
                                $map = [
                                    '0'=>'未处理',
                                    '1'=>'已通过'
                                ];
                                if ($model->type=='reduce' && !$model->status){
                                    return \yii\helpers\Html::a('通过',\yii\helpers\Url::to(['doctors/moneylog/status-succ','id'=>$model->id]),[
//                                        'id'=>'moneylog',
//                                        'type'=>'button',
//                                        'target'=>'_blank',
                                        'class'=>'btn btn-sm btn-info',
                                        'data-confirm' => '此操作将会本条数据标记为已通过，且不可逆？',
                                        'data-method'=>'get',
                                        'data-ajax'=>'1'
                                    ]);
                                }
                                return $map[$model->status];
                            }
                        ],
//                        'desc',
                        [
                            'attribute'=>'money',
                            'filter'=>false
                        ],
                        [
                            'label'=>'结算日期',
                            'filter'=>false,
                            'value'=>'relationPdmlog.date'
                        ],
                        [
                            'class' => \backend\grid\DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        // 'updated_at',
                        [
                            'class' => ActionColumn::className(),
                            'template'=>'{view-layer}'
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
