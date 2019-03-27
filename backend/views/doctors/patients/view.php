<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorPatients */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-patients-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'invite',
            'phone',
            'sex',
            'age',
            'id_number',
            [
                'label'=>'所属医院',
                'attribute'=>'hospital.hospital_name'
            ],
            [
                'label'=>'所属医生',
                'attribute'=>'doctor.name'
            ],
            [
                'attribute'=>'is_transfer',
                'value'=>function($model){
                    $map = ['否','是'];
                    return $map[$model->is_transfer];
                }
            ],
            [
                'attribute' => 'transferDoctor.name',
                'label' => '原医生'
            ],
            [
                'label' => '原医院',
                'value' => function ($model) {
                    if ($model->transferDoctor){
                        return \common\models\doctors\DoctorHospitals::findOne(['id'=>$model->transferDoctor->hospital_id])->hospital_name;
                    }
                },
            ],
            'desc:ntext',
            'remark:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
