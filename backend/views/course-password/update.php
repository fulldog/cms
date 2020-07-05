<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\CoursePassword */

$this->params['breadcrumbs'] = [
    ['label' => '密码列表', 'url' => Url::to(['index'])],
    ['label' => $parent->title],
    ['label' => yii::t('app', 'Update') . '密码'],
];
?>
<?= $this->render('_form', [
    'model' => $model,
    'parent'=>$parent
]) ?>
