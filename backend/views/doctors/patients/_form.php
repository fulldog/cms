<?php

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorPatients */
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
          <?= $form->field($model->hospital, 'hospital_name')->textInput(['disabled'=>'disabled'])->label('所属医院')?>

        <?if(!empty($model->doctor)):?>
          <?= $form->field($model->doctor, 'name')->textInput(['disabled'=>'disabled'])->label('所属医生') ?>
        <?else:?>
            <?= $form->field($model, 'doctor_id')->dropDownList(\common\helpers\CommonHelpers::getDoctorByHid($model->hospital_id))?>
        <?endif;?>
          <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'age')->textInput() ?>
          <?= $form->field($model, 'sex')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'is_transfer')->radioList(['否', '是'],['itemOptions'=>['disabled'=>'disabled']]) ?>

          <?= $form->field($model, 'id_number')->textInput() ?>

          <?= $form->field($model, 'desc')->textarea(['rows' => 6]) ?>
          <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
          <?= $form->defaultButtons() ?>
          <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>