<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Course */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-view">

  <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'desc',
            [
                'attribute' => 'tags',
                'format' => 'raw',
                'value' => function ($model) {
                    return \common\models\Course::$_tags[$model->tags] ?? '-';
                }
            ],
            'price',
            [
                'attribute' => 'wechat_img',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<img style='max-width:200px;max-height:200px' src='" . $model->wechat_img . "' >";
                }
            ],
            [
                'attribute' => 'thumb',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<img style='max-width:200px;max-height:200px' src='" . $model->thumb . "' >";
                }
            ],
            [
                'attribute' => 'video',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<video style='max-width:200px;max-height:200px' src='" . $model->video . "'  controls=\"controls\"></video>";
                }
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return \common\libs\Constants::getStatusItems($model->status);
                }
            ],
            [
                'attribute' => 'recommend',
                'value' => function ($model) {
                    return \common\libs\Constants::getYesNoItems($model->recommend);
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
