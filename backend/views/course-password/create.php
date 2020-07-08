<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\CoursePassword */

$this->params['breadcrumbs'] = [
    ['label' => '密码列表', 'url' => Url::to(['index','CoursePasswordSearch[course_id]'=>$parent->id])],
    ['label' => $parent->title],
    ['label' => yii::t('app', 'Create') . '密码'],
];
?>
<?= $this->render('_form', [
    'model' => $model,
    'parent' => $parent,
]) ?>

