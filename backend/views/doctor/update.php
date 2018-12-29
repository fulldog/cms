<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model frontend\models\DoctorInfos */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Doctor Infos'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Update') . yii::t('app', 'Doctor Infos')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
