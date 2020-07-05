<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\VoteChild */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '投票详情', 'url' => ['index']];
$this->params['breadcrumbs'][] = $parent->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-child-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'vote.title',
            'title',
            'desc',
            'pv',
            'vote_count',
            [
                'attribute' => 'img',
                'format' => 'raw',
                'value' => function($model){
                    return "<img style='max-width:200px;max-height:200px' src='" . $model->img . "' >";
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
