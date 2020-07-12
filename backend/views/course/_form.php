<?php

use backend\widgets\ActiveForm;
use common\models\Category;
use common\libs\Constants;
use common\widgets\JsBlock;
use backend\widgets\Ueditor;
use backend\widgets\webuploader\Webuploader;
use common\helpers\Util;

/* @var $this yii\web\View */
/* @var $model common\models\Course */
/* @var $form backend\widgets\ActiveForm */
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'enctype' => 'multipart/form-data',
                        'class' => 'form-horizontal'
                    ]
                ]); ?>
                <div class="hr-line-dashed"></div>
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                        <div class="hr-line-dashed"></div>

                <?= $form->field($model, 'cid', ['size'=>10])->dropDownList(\common\models\CourseCate::getAllCates())?>
              <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'price', ['size'=>10])->textInput(['maxlength' => true])?>
              <div class="hr-line-dashed"></div>

                <?= $form->field($model, 'tags')->dropDownList(\common\models\Course::$_tags) ?>
              <div class="hr-line-dashed"></div>
                        <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>
                        <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'thumb')->imgInput(['style' => 'max-width:200px;max-height:200px']); ?>
              <div class="hr-line-dashed"></div>

              <?= $form->field($model, 'wechat_img')->imgInput(['style' => 'max-width:200px;max-height:200px']); ?>

                        <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'banner')->imgInput(['style' => 'max-width:200px;max-height:200px']); ?>

              <div class="hr-line-dashed"></div>


                <?= $form->field($model, 'video')->fileInput(); ?>

              <div class="hr-line-dashed"></div>

                <?= $form->field($model, 'status', [])->radioList(Constants::getArticleStatus()); ?>
                        <div class="hr-line-dashed"></div>

                <?= $form->field($model, 'recommend', [])->radioList([
                  1=>'推荐',0=>'不推荐'
                ]); ?>
                        <div class="hr-line-dashed"></div>

                        <?= $form->defaultButtons() ?>
                    <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>