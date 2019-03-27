<?php

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorHospitals */
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
          <?= $form->field($model, 'hospital_name')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'grade')->textInput(['maxlength' => true]) ?>
<!--          --><?//= $form->field($model, 'avatar')->imgInput() ?>
          <?= $form->field($model, 'avatar')->widget(\backend\widgets\webuploader\Webuploader::className())
              ->hint('*请勿上传多张图片！！！',[
                  'style'=>'margin-left:19%;color:red;',
              ]) ?>
          <? if (!\Yii::$app->user->identity->hospital_id): ?>
              <?= $form->field($model, 'status')->dropDownList(\common\models\doctors\My::_getStatusAll()) ?>
              <?= $form->field($model, 'recommend')->dropDownList(\common\models\doctors\My::_getStatusAll('recommend')) ?>
              <?= $form->field($model, 'transfer')->dropDownList(\common\models\doctors\My::_getStatusAll('transfer')) ?>
          <? endif; ?>

          <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'area')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'imgs')->widget(\backend\widgets\webuploader\Webuploader::className()); ?>
          <?= $form->defaultButtons() ?>
          <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>