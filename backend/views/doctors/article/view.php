<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorArticle */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-article-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
//            'hospital_id',
            'title',
            [
                'attribute'=>'status',
                'filter'=>[0=>'关闭',1=>'打开'],
                'value'=>function($model){
                    $map = [0=>'关闭',1=>'打开'];
                    return $map[$model->status];
                }
            ],
            [
                'label'=>'img',
                'format'=>'raw',
                'value'=>function($model){
                    return \yii\helpers\Html::img($model->img,['style'=>'height:100px;']);
                }
            ],
            'desc',
            'keywords',
            [
                'label'=>'content',
                'format'=>'raw',
                'value'=>function($model){
                    return $model->content;
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
