<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorInfos */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doctor-infos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uid',
            [
                'label'=>'手机号',
                'attribute' => 'relatedUser.username',
//                'value'=>function($model){
//                    $info = \common\models\doctors\DoctorUser::findOne(['id'=>$model->uid]);
//                    if ($info){
//                        return $info->username;
//                    }
//                },
            ],
            [
                'label'=>'所属医院',
                'attribute' => 'hospital.hospital_name',
//                'value'=>function($model){
//                    $info = \common\models\doctors\DoctorHospitals::findOne(['id'=>$model->hospital_id]);
//                    if ($info){
//                        return $info->hospital_name;
//                    }
//                },
            ],
            'name',
            [
                'attribute' => 'avatar',
                'format' => 'raw',
                'value' => function($model){
                    if ($model->avatar){
                        return "<img style='max-width:200px;max-height:200px' src='" . $model->avatar . "' >";
                    }
                }
            ],
            'doctor_type',
            'role',
            [
                'attribute'=>'status',
                'value'=>function($model){
                    return $model->getStatus();
                },
            ],
            [
                'attribute'=>'recommend',
                'value'=>function($model){
                    return $model->getRecommend();
                },
            ],
            'province',
            'city',
            'area',
            'address',
            [
                'attribute' => 'certificate',
                'format' => 'raw',
                'value' => function($model){
                    $imgs = '';
                    if ($model->certificate && !empty($model->certificate)){
                        foreach ($model->certificate as $v){
                            $imgs .="<img style='max-width:200px;max-height:200px' src='" . $v . "' >";
                        }
                    }
                    return $imgs;
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
