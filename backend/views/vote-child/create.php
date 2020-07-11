<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\VoteChild */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', '投票活动'), 'url' => Url::to(['vote/index'])],
    ['label' => $parent->title, 'url' => Url::to(['index','VoteChildSearch[vid]' => $parent->id])],
    ['label' => yii::t('app', 'Create') . yii::t('app', '投票')],
];
?>
<?= $this->render('_form', [
    'model' => $model,
    'parent'=>$parent
]) ?>

