<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorPatients */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Doctor Patients'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Update') . yii::t('app', 'Doctor Patients')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
