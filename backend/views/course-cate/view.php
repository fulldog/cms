<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CourseCate */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '课程分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-cate-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'alias_name',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<img style='max-width:200px;max-height:200px' src='" . $model->alias_name . "' >";
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
