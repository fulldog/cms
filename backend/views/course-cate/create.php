<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\CourseCate */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', '课程分类'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Create') . yii::t('app', '分类')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

