<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorNotices */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '系统公告', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-notices-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'hospital.hospital_name',
            'notice',
            [
                'attribute' => 'status',
                'value'=>function($model){
                    $map = ['关闭','打开'];
                    return $map[$model->status];
                }
            ],
//            'to',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
