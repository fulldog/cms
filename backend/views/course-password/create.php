<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\CoursePassword */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', '课程列表'), 'url' => Url::to(['course/index'])],
    ['label' => $parent->title, 'url' => Url::to(['index','CoursePasswordSearch[course_id]' => $parent->id])],
    ['label' => yii::t('app', 'Create') . '密码'],
];
?>
<?= $this->render('_form', [
    'model' => $model,
    'parent' => $parent,
]) ?>

