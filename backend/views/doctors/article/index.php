<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\doctors\DoctorArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = yii::t('app_doctor', 'Doctor Article');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget([
                    'template'=>'{refresh} {create}'
                ]) ?>
<!--                --><?//=$this->render('_search', ['model' => $searchModel]); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
//                        ['class' => CheckboxColumn::className()],

//                        'id',
//                        'hospital_id',
                        'title',
                        [
                            'label'=>'img',
                            'format'=>'raw',
                            'value'=>function($model){
                                return \yii\helpers\Html::img($model->img,['style'=>'height:100px;']);
                            }
                        ],
                        [
                            'attribute'=>'status',
                            'filter'=>[0=>'关闭',1=>'打开'],
                            'value'=>function($model){
                                $map = [0=>'关闭',1=>'打开'];
                                return $map[$model->status];
                            }
                        ],
//                        'desc',
//                        'keywords',
                        // 'content:ntext',
                        [
                            'attribute'=>'created_at',
                            'filter'=>false,
                            'value'=>function($model){
                                return date('Y-m-d H:i:s',$model->created_at);
                            }
                        ],
                        [
                            'attribute'=>'updated_at',
                            'filter'=>false,
                            'value'=>function($model){
                                return date('Y-m-d H:i:s',$model->updated_at);
                            }
                        ],
                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
