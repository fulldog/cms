<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Vote */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', '投票活动'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Create') . yii::t('app', '活动')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

