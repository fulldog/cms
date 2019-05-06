<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorMoneylog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Moneylogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-moneylog-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'hospital.hospital_name'
            ],
//            [
//                'attribute'=>'doctor.name'
//            ],
//            [
//                'attribute'=>'patient.name',
//                'label'=>'病人名称'
//            ],
//            [
//                'attribute'=>'patient.id_number',
//                'label'=>'病人身份证'
//            ],
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
                'value'=>function($model){
                    $map = [
                        '0'=>'未处理',
                        '1'=>'已通过'
                    ];
                    return $map[$model->status];
                }
            ],
            'desc',
            'money',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'label'=>'',
                'value'=>''
            ],
            [
                'label'=>'病人数据明细',
                'value'=>''
            ],
            'relationPdmlog.name',
            'relationPdmlog.id_card',
            'relationPdmlog.type',
            'relationPdmlog.desc',
            'relationPdmlog.money',
            'relationPdmlog.date',
            'relationPdmlog.created_at:datetime',
        ],
    ]) ?>

</div>
