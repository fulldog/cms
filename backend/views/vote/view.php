<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Vote */

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
            [
                'attribute' => 'recommend',
                'value' => function ($model) {
                    return \common\libs\Constants::getYesNoItems($model->recommend);
                }
            ],
            [
                'attribute'=>'vote_count',
                'value'=>function($model){
                    return \common\models\VoteChild::find()->where(['vid' => $model->id])->sum('vote_count');
                }
            ],
            [
                'attribute'=>'pv',
                'value'=>function($model){
                    return \common\models\VoteChild::find()->where(['vid' => $model->id])->sum('pv');
                }
            ],
            [
                'attribute' => 'img',
                'format' => 'raw',
                'value' => function($model){
                    return "<img style='max-width:200px;max-height:200px' src='" . $model->img . "' >";
                }
            ],
            [
                'attribute' => 'banner',
                'format' => 'raw',
                'value' => function($model){
                    return "<img style='max-width:200px;max-height:200px' src='" . $model->banner . "' >";
                }
            ],
            'updated_at:datetime',
            'created_at:datetime',
        ],
    ]) ?>

</div>
