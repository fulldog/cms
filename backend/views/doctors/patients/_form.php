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
        <!--                    --><? //= $form->field($model, 'hospital_id')->textInput() ?>

        <!--                        --><? //= $form->field($model, 'doctor_id')->textInput() ?>


          <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'age')->textInput() ?>
          <?= $form->field($model, 'sex')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'is_transfer')->dropDownList(['否','是']) ?>

          <?= $form->field($model, 'id_number')->textInput() ?>

          <?= $form->field($model, 'desc')->textarea(['rows' => 6]) ?>

<!--          --><?//= $form->field($model, 'created_at')->textInput() ?>

<!--          --><?//= $form->field($model, 'updated_at')->textInput() ?>


          <?= $form->defaultButtons() ?>
          <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>