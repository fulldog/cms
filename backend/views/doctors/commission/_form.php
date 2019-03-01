<?php

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorCommission */
/* @var $form backend\widgets\ActiveForm */
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'class' => 'form-horizontal'
                    ]
                ]); ?>
                <div class="hr-line-dashed"></div>
                    <?= $form->field($model, 'hospital_id')->dropDownList(\common\models\doctors\DoctorHospitals::find()->getHospitals()) ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'patient_id')->dropDownList(\common\models\doctors\DoctorPatients::find()->getPatients()) ?>
                        <div class="hr-line-dashed"></div>

<!--                        --><?//= $form->field($model, 'extend1')->textInput(['maxlength' => true]) ?>
<!--                        <div class="hr-line-dashed"></div>-->
                        <?= $form->field($model, 'point')->textInput(['type'=>'number'])?>
                        <div class="hr-line-dashed"></div>

<!--                        --><?//= $form->field($model, 'extend2')->textInput(['maxlength' => true]) ?>
<!--                        <div class="hr-line-dashed"></div>-->

<!--                        --><?//= $form->field($model, 'extend3')->textInput(['maxlength' => true]) ?>
<!--                        <div class="hr-line-dashed"></div>-->

<!--                        --><?//= $form->field($model, 'created_at')->textInput() ?>
<!--                        <div class="hr-line-dashed"></div>-->
<!---->
<!--                        --><?//= $form->field($model, 'updated_at')->textInput() ?>
<!--                        <div class="hr-line-dashed"></div>-->

                        <?= $form->defaultButtons() ?>
                    <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>