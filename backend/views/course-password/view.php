<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CoursePassword */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Course Passwords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-password-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'course_id',
            'password',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
