<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Course */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', '课程列表'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Create') . yii::t('app', '课程')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

