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
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                    ]
                ]); ?>
                <div class="hr-line-dashed"></div>
<!--                        --><?//= $form->field($model, 'uid')->dropDownList()->label('关联用户') ?>
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<!--                        --><?//= $form->field($model, 'avatar')->imgInput(['style' => 'max-width:200px;max-height:200px']) ?>
                        <?= $form->field($model, 'avatar')->widget(\backend\widgets\webuploader\Webuploader::className())
                            ->hint('*请勿上传多张图片！！！',[
                                'style'=>'margin-left:19%;color:red;',
                            ]) ?>
                        <?= $form->field($model, 'hospital_id')->dropDownList(\common\models\doctors\DoctorHospitals::find()->getHospitals())->label('医院')?>
                        <?= $form->field($model, 'doctor_type')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'recommend')->dropDownList(\common\models\doctors\My::_getStatusAll('recommend')) ?>
                        <?= $form->field($model, 'status')->dropDownList([0=>'待审核',1=>'通过',2=>'拒绝']) ?>
                        <?= $form->field($model, 'role')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'ills')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'area')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'certificate')->widget(\backend\widgets\webuploader\Webuploader::className()); ?>

                        <?= $form->defaultButtons() ?>
                    <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>