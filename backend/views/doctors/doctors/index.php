<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\doctors\DoctorInfosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = yii::t('app_doctor', 'Doctor Infos');
$this->params['breadcrumbs'][] = yii::t('app_doctor', 'Doctor Infos');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget(['template' => "{refresh}  {delete}"]) //{create}?>
                <?php //$this->render('_search', ['model' => $searchModel]); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => CheckboxColumn::className()],

//                        'id',
//                        'uid',
                        'name',
//                        [
//                            'label'=>'手机号',
//                            'value'=>'relatedUser.username',
//                            'filter'=>false
//                        ],
                        [
                            'label'=>'所属医院',
                            'value'=>'hospital.hospital_name',
                            'filter'=>false
                        ],
                        [
                            'attribute'=>'status',
                            'value'=>function($model){
                                return $model->getStatus();
                            },
                            'format'=>'raw',
                            'filter'=>\common\models\doctors\My::_getStatusAll()
                        ],
                        [
                            'attribute'=>'recommend',
                            'value'=>function($model){
                                return $model->getRecommend();
                            },
                            'format'=>'raw',
                            'filter'=>\common\models\doctors\My::_getStatusAll('recommend')
                        ],
                        'doctor_type',
                        'role',
                        'ills',
                        [
                            'label'=>'住址',
                            'value'=>function($model){
                                return $model->province.$model->city.$model->area.$model->address;
                            },
                        ],
                        'money',
//                        'province',
//                        'city',
//                        'area',
//                        'address',
                        [
                            'class' => \backend\grid\DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
//                        [
//                            'class' => \backend\grid\DateColumn::className(),
//                            'attribute' => 'updated_at',
//                        ],

                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
