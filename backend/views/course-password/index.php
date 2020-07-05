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
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '课程密码';
$this->params['breadcrumbs'][] = '课程密码';
$this->params['breadcrumbs'][] = $parent->title;
?>
<div class="row">
  <div class="col-sm-12">
    <div class="ibox">
        <?= $this->render('/widgets/_ibox-title') ?>
      <div class="ibox-content">
          <?= Bar::widget(
              [
                  'buttons' => [
                      'create' => function () use ($parent) {
                          return Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), Url::to(['course-password/create', 'CoursePasswordSearch[pid]' => $parent->id]), [
                              'title' => Yii::t('app', 'Create'),
                              'data-pjax' => '0',
                              'class' => 'btn btn-white btn-sm',
                          ]);
                      },
                      'delete' => ''
                  ]
              ]
          ) ?>
          <?= GridView::widget([
              'dataProvider' => $dataProvider,
              'columns' => [
                  ['class' => CheckboxColumn::className()],

//                        'id',
                  [
                      'filter' => false,
                      'header' => '所属课程',
                      'headerOptions' => ['width' => '100'],
                      'value' => 'course.title'
                  ],
                  'password',
                  [
                      'attribute' => 'status',
                      'format' => 'raw',
                      'value' => function ($model) {
                          /* @var $model backend\models\Article */
                          return Html::a(Constants::getYesNoItems($model['status']), '', [
                              'class' => 'btn btn-xs btn-rounded ' . ($model['status'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default'),
                          ]);
                      },
                      'filter' => false,
                  ],
                  'used_by',
                  [
                      'class' => DateColumn::className(),
                      'attribute' => 'created_at',
                  ],
                  [
                      'class' => DateColumn::className(),
                      'attribute' => 'updated_at',
                  ],
//                  ['class' => ActionColumn::className(),],
              ],
          ]); ?>
      </div>
    </div>
  </div>
</div>
