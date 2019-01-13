<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorInfos */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app_doctor', 'Doctor Infos'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app_doctor', 'Update')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
