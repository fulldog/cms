<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorCommission */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app_doctor', 'Doctor Commission'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Create') . yii::t('app_doctor', 'Doctor Commission')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

