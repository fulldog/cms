<?php

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorInfos */
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
                        <?= $form->field($model, 'uid')->dropDownList()->label('关联用户') ?>

                        <?= $form->field($model, 'hospital_id')->textInput() ?>

                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'doctor_type')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'role')->textInput(['maxlength' => true]) ?>


                        <?= $form->field($model, 'certificate')->widget(\backend\widgets\webuploader\Webuploader::className()); ?>

                        <?= $form->defaultButtons() ?>
                    <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>