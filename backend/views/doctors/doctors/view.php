<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorInfos */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-infos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uid',
            'hospital_id',
            'name',
            'doctor_type',
            'role',
            'hospital_location',
            'hospital_name',
            'certificate:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
