<?php

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CourseChild */
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
                    <?php
                      echo $form->field($model, 'course_id')->hiddenInput(['value'=>$parent->id])->label(false);
                    ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>
                        <div class="hr-line-dashed"></div>

                <?= $form->field($model, 'thumb')->imgInput(['style' => 'max-width:200px;max-height:200px']); ?>
                        <div class="hr-line-dashed"></div>

                <?= $form->field($model, 'video')->fileInput(); ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->defaultButtons() ?>
                    <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>