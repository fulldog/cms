<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorHospitals */

$this->title = $model->hospital_name;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Hospitals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-hospitals-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'hospital_name',
            [
                'attribute'=>'status',
                'value'=>function($model){
                    return $model->getStatus();
                },
            ],
            'grade',
            'province',
            'city',
            'area',
            'address',
            [
                'attribute' => 'imgs',
                'format' => 'raw',
                'value' => function($model){
                    $imgs = '';
                    if ($model->imgs){
                        foreach ($model->imgs as $v){
                            $imgs .="<img style='max-width:200px;max-height:200px' src='" . $v . "' >";
                        }
                    }
                    return $imgs;
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',

        ],
    ]) ?>

</div>
