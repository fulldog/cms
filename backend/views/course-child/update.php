<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\CourseChild */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', '课时列表'), 'url' => Url::to(['index'])],
    ['label' => $parent->title],
    ['label' => yii::t('app', 'Update') . yii::t('app', '课时')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
    'parent'=>$parent
]) ?>
