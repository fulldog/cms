<?php

use yii\helpers\Html;
use backend\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorInfosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doctor-infos-search ibox-heading row search" style="margin-top: 5px;padding-top:5px">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'uid') ?>

    <?= $form->field($model, 'hospital_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'doctor_type') ?>

    <?php // echo $form->field($model, 'role') ?>

    <?php // echo $form->field($model, 'hospital_location') ?>

    <?php // echo $form->field($model, 'hospital_name') ?>

    <?php // echo $form->field($model, 'certificate') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="col-sm-3">
        <div class="col-sm-6">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary btn-block']) ?>
        </div>
        <div class="col-sm-6">
            <?= Html::a('Reset', Url::to(['index']), ['class' => 'btn btn-default btn-block']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
