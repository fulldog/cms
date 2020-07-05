<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;
use backend\grid\DateColumn;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VoteChildSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '投票详情';
$this->params['breadcrumbs'][] = yii::t('app', '投票详情');
$this->params['breadcrumbs'][] =$parent->title;
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
                                return \yii\helpers\Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), \yii\helpers\Url::to(['vote-child/create', 'VoteChildSearch[vid]' => $parent->id]), [
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
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => CheckboxColumn::className()],
//                        'id',
//                        'vid',
//                        'vote.title',
                        'title',
//                        'desc',
                        [
                            'attribute' => 'img',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                if ($model->img == '') {
                                    $num = \common\libs\Constants::YesNo_No;
                                } else {
                                    $num = \common\libs\Constants::YesNo_Yes;
                                }
                                return \yii\helpers\Html::a(\common\libs\Constants::getYesNoItems($num), $model->img ? $model->img : 'javascript:void(0)', [
                                    'img' => $model->img ? $model->img : '',
                                    'class' => 'thumbImg',
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]);
                            },
                            'filter' => false,
                        ],
                        [
                            'filter' => false,
                            'attribute' => 'pv',
                        ],
                        [
                            'filter' => false,
                            'attribute' => 'vote_count',
                        ],
//                        [
//                            'class' => DateColumn::className(),
//                            'attribute' => 'created_at',
//                        ],
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
