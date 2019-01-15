<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;
use backend\grid\DateColumn;
/* @var $this yii\web\View */
/* @var $searchModel common\models\doctors\DoctorHospitalsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = yii::t('app_doctor', 'Doctor Hospitals');
$this->params['breadcrumbs'][] = yii::t('app_doctor', 'Doctor Hospitals');
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

//                        [
//                            'attribute' => 'id',
//                        ],
                        'hospital_name',
                        'province',
                        [
                            'attribute'=>'status',
                            'value'=>function($model){
                                return $model->getStatus();
                            },
                            'format'=>'raw',
                            'filter'=>\common\models\doctors\My::_getStatusAll()
                        ],
                        'city',
                        'area',
//                        [
//                            'attribute' => 'address',
//                            'value'=>function($model){
//                                return $model->province.'-'.$model->city.'-'.$model->area.'-'.$model->address;
//                            }
//                        ],
                        'address',
                        'grade',
                        [
                            'attribute'=>'recommend',
                            'value'=>function($model){
                                return $model->getRecommend();
                            },
                            'format'=>'raw',
                            'filter'=>\common\models\doctors\My::_getStatusAll('recommend')
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'updated_at',
                        ],
                        [
//                            'attribute' => '',
                            'label'=>'管理账号',
                            'format'=>'raw',
                            'value'=>function($model){
                                  if ($model->relatedAdmin){
                                    return $model->relatedAdmin->username;
                                  }else{
                                    return \yii\helpers\Html::a('创建',\yii\helpers\Url::to(['doctors/dadmin-user/hospital','hospital_id'=>$model->id]),[
                                        'id'=>'aotu-admin',
                                        'type'=>'button',
//                                        'target'=>'_blank',
                                        'class'=>'btn btn-sm btn-info',
//                                        'data-confirm' => '此操作将会更新医院为已通过审核并创建管理员？',
                                    ]);
                                  }
                            }
                        ],
//                         'imgs:ntext',

                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
