<?php

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VoteChild */
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
                    <?= $form->field($model, 'vid')->hiddenInput(['value'=>$parent->id])->label(false) ?>

                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'pv')->textInput() ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'vote_count')->textInput() ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->field($model, 'img')->imgInput(['style' => 'max-width:200px;max-height:200px']); ?>
                        <div class="hr-line-dashed"></div>

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