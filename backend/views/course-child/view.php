<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CourseChild */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Course Children', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-child-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'course_id',
            'title',
            'desc',
            [
                'attribute' => 'thumb',
                'format' => 'raw',
                'value' => function($model){
                    return "<img style='max-width:200px;max-height:200px' src='" . $model->thumb . "' >";
                }
            ],
            [
                'attribute' => 'video',
//                'format' => 'raw',
//                'value' => function($model){
//                    return "<video style='max-width:200px;max-height:200px' src='" . $model->video . "'  controls=\"controls\"></video>";
//                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
