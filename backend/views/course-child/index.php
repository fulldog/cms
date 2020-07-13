<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;
use backend\grid\DateColumn;
use backend\grid\SortColumn;
use backend\widgets\ActiveForm;
use common\widgets\JsBlock;
use yii\helpers\Url;
use common\models\Category;
use common\libs\Constants;
use yii\helpers\Html;
use yii\widgets\Pjax;
use backend\grid\StatusColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CourseChildSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label'=>yii::t('app',  '课程列表'),'url' => Url::to(['course/index'])];
$this->params['breadcrumbs'][] = $parent->title;
$this->params['breadcrumbs'][] = '课时列表';
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget(
                    [
                        'buttons' => [
                            'create' => function () use($parent) {
                                return Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), Url::to(['course-child/create', 'CourseChildSearch[course_id]' => $parent->id]), [
                                    'title' => Yii::t('app', 'Create'),
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-white btn-sm',
                                ]);
                            },
                        ]
                    ]
                ) ?>
<!--                --><?//=$this->render('_search', ['model' => $searchModel]); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
//                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => CheckboxColumn::className()],

//                        'id',
//                        'course_id',
                        [
                            'filter'=>false,
                            'header'=>'所属课程',
                            'headerOptions'=>['width'=>'100'],
                            'value'=>'course.title'
                        ],
                        'title',
                        'desc',
                        [
                            'attribute' => 'thumb',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                if ($model->thumb == '') {
                                    $num = Constants::YesNo_No;
                                } else {
                                    $num = Constants::YesNo_Yes;
                                }
                                return Html::a(Constants::getYesNoItems($num), $model->thumb ? $model->thumb : 'javascript:void(0)', [
                                    'img' => $model->thumb ? $model->thumb : '',
                                    'class' => 'thumbImg',
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]);
                            },
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'video',
                            'format' => 'raw',
                            'value' => function($model){
                                if ($model->video){
                                    return "<video style='max-width:200px;max-height:200px' src='" . $model->video . "'  controls=\"controls\"></video>";
                                }
                                return '';
                            },
                            'filter' => false,
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
//                        [
//                            'class' => DateColumn::className(),
//                            'attribute' => 'updated_at',
//                        ],

                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
