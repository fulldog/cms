<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Vote */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '投票活动', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'desc',
            'start_time:datetime',
            'end_time:datetime',
            'vote_count',
            'pv',
            [
                'attribute' => 'img',
                'format' => 'raw',
                'value' => function($model){
                    return "<img style='max-width:200px;max-height:200px' src='" . $model->img . "' >";
                }
            ],
            'updated_at:datetime',
            'created_at:datetime',
        ],
    ]) ?>

</div>
