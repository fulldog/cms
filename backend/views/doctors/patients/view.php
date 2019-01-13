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
            'phone',
            'sex',
            'age',
            'id_number',
            'desc:ntext',
            [
                'label'=>'所属医院',
                'value'=>function($model){
                    $info = \common\models\doctors\DoctorHospitals::findOne(['id'=>$model->hospital_id]);
                    if ($info){
                        return $info->hospital_name;
                    }
                },
            ],
            [
                'label'=>'所属医生',
                'value'=>function($model){
                    $info = \common\models\doctors\DoctorInfos::findOne(['id'=>$model->doctor_id]);
                    if ($info){
                        return $info->name;
                    }
                },
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
