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
            'desc:ntext',
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
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
