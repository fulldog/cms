<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorHospitals */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app_doctor', 'Doctor Hospitals'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app_doctor', 'Update') . yii::t('app', 'Doctor Hospitals')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
