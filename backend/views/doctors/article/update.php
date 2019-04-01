<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorArticle */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app_doctor', 'Doctor Article'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Update') . yii::t('app_doctor', 'Doctor Article')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
