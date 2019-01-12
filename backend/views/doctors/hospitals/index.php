<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;
use backend\grid\DateColumn;
/* @var $this yii\web\View */
/* @var $searchModel common\models\doctors\DoctorHospitalsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Doctor Hospitals';
$this->params['breadcrumbs'][] = yii::t('app', 'Doctor Hospitals');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?php //$this->render('_search', ['model' => $searchModel]); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => CheckboxColumn::className()],

                        [
                            'attribute' => 'id',
                        ],
                        'hospital_name',
//                        'province',
//                        'city',
//                        'area',
                        [
                            'attribute' => 'address',
                            'value'=>function($model){
                                return $model->province.'-'.$model->city.'-'.$model->area.'-'.$model->address;
                            }
                        ],
//                        'address',
                        'grade',
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'updated_at',
                        ],
//                         'imgs:ntext',

                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
