<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorMoneylog */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app_doctor', 'Doctor Moneylog'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Create') . yii::t('app_doctor', 'Doctor Moneylog')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

