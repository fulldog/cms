<?php

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorNotices */
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
                    <?= $form->field($model, 'hospital_id')->dropDownList(\common\models\doctors\DoctorHospitals::find()->getHospitals($model->hospital_id)) ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'notice')->textInput(['maxlength' => true]) ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'status')->dropDownList([0=>'关闭',1=>'打开']) ?>
                        <div class="hr-line-dashed"></div>

<!--                        --><?//= $form->field($model, 'to')->dropDownList(['user'=>'用户','admin'=>'管理员']) ?>
<!--                        <div class="hr-line-dashed"></div>-->

                        <?= $form->defaultButtons() ?>
                    <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>