<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\doctors\CommissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = yii::t('app_doctor', 'Doctor Commission');
$this->params['breadcrumbs'][] = yii::t('app_doctor', 'Doctor Commission');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
<!--                --><?//=$this->render('_search', ['model' => $searchModel]); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => CheckboxColumn::className()],
                        [
                            'label'=>'所属医院',
                            'value'=>function($model){
                                return $model->hospital->hospital_name;
                            },
                            'filter'=>\common\models\doctors\DoctorHospitals::find()->getHospitals(),
                            'attribute'=>'hospital_id'
                        ],
                        [
                            'label'=>'病人名称',
                            'attribute'=>'patient.name'
                        ],
                        [
                            'label'=>'身份证',
                            'attribute'=>'patient.id_number'
                        ],
                        [
                            'attribute'=>'point',
                        ],
                        // 'extend2',
                        // 'extend3',
                        [
                            'class' => \backend\grid\DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => \backend\grid\DateColumn::className(),
                            'attribute' => 'updated_at',
                        ],

                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
