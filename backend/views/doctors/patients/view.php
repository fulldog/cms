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
            'hospital_id',
            'doctor_id',
            'is_transfer',
            'name',
            'tel',
            'sex',
            'id_number',
            'desc:ntext',
            'created_at',
            'updated_at',
            'age',
        ],
    ]) ?>

</div>
