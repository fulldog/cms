<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorCommission */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Commissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-commission-view">

<!--    <h1>--><?//= Html::encode($this->title) ?><!--</h1>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            [
                'label'=>'所属医院',
                'attribute'=>'hospital.hospital_name'
            ],
            [
                'label'=>'病人名称',
                'attribute'=>'patient.name'
            ],
            [
                'label'=>'身份证',
                'attribute'=>'patient.id_number'
            ],
            [
                'attribute'=>'point',
                'value'=>function($model){
                    return $model->point.'%';
                },
            ],
//            'extend1',
//            'extend2',
//            'extend3',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
