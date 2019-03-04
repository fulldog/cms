<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorNotices */

$this->params['breadcrumbs'] = [
    ['label' => '系统公告', 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Create') . '系统公告'],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

