<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\VoteChild */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', '投票详情'), 'url' => Url::to(['index','VoteChildSearch[vid]'=>$parent->id])],
    ['label'=>$parent->title],
    ['label' => yii::t('app', 'Create') . yii::t('app', '投票')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
    'parent'=>$parent
]) ?>

