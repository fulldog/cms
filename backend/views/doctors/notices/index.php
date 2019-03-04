<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\doctors\DoctorNoticesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统通告';
$this->params['breadcrumbs'][] = '系统通告';
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
//                        ['class' => CheckboxColumn::className()],
//                        'id',
                        [
                            'attribute' => 'hospital_id',
                            'filter'=>\common\models\doctors\DoctorHospitals::find()->getHospitals(),
                            'value'=>function($model){
                                  if (!empty($model->hospital)){
                                    return $model->hospital->hospital_name;
                                  }
                            }
                        ],
                        'notice',
                        [
                            'attribute' => 'status',
                            'filter'=>['关闭','打开'],
                            'value'=>function($model){
                                $map = ['关闭','打开'];
                                return $map[$model->status];
                            }
                        ],
//                        [
//                            'attribute' => 'to',
//                            'filter'=>['user'=>'用户','admin'=>'管理员'],
//                            'value'=>function($model){
//                                if ($model->to){
//                                    $map = ['user'=>'用户','admin'=>'管理员'];
//                                    return $map[$model->to];
//                                }
//                            }
//                        ],
                        [
                            'class' => \backend\grid\DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        // 'updated_at',

                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
