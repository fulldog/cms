<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorHospitals */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Hospitals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-hospitals-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'city',
            'address',
            'levels',
            'created_at',
            'updated_at',
            'imgs:ntext',
        ],
    ]) ?>

</div>
