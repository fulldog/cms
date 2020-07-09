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
/* @var $searchModel common\models\CourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '课程列表';
$this->params['breadcrumbs'][] = yii::t('app', '课程列表');
$config = yii\helpers\ArrayHelper::merge(
    require Yii::getAlias("@frontend/config/main.php"),
    require Yii::getAlias("@frontend/config/main-local.php")
);
$prettyUrl = false;
if( isset( $config['components']['urlManager']['enablePrettyUrl'] ) ){
    $prettyUrl = $config['components']['urlManager']['enablePrettyUrl'];
}
$suffix = "";
if( isset( $config['components']['urlManager']['suffix'] ) ){
    $suffix = $config['components']['urlManager']['suffix'];
}
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

//                        [
//                            'attribute' => 'id',
//                        ],
                        'title',
//                        'desc',
                        [
                            'attribute' => 'cid',
                            'label' => Yii::t('app', 'Category'),
                            'value' => function ($model) {
                                return $model->category ? $model->category->name : Yii::t('app', 'uncategoried');
                            },
                            'filter' => \common\models\CourseCate::getAllCates(),
                        ],
                        'tags',
                        [
                            'attribute' => 'price',
                            'filter' => false,
                        ],
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
                            'attribute' => 'wechat_img',
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

//                        [
//                            'attribute' => 'video',
//                            'format' => 'raw',
//                            'value' => function($model){
//                                return "<video style='max-width:200px;max-height:200px' src='" . $model->video . "'  controls=\"controls\"></video>";
//                            }
//                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                /* @var $model backend\models\Article */
                                return Html::a(Constants::getArticleStatus($model['status']), ['update', 'id' => $model['id']], [
                                    'class' => 'btn btn-xs btn-rounded ' . ( $model['status'] == Constants::YesNo_Yes ? 'btn-info' : 'btn-default' ),
                                    'data-confirm' => $model['status'] == Constants::YesNo_Yes ? Yii::t('app', '确定要删除吗？') : Yii::t('app', '确定要发布吗？'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-params' => [
                                        $model->formName() . '[status]' => $model['status'] == Constants::YesNo_Yes ? Constants::YesNo_No : Constants::YesNo_Yes
                                    ]
                                ]);
                            },
                            'filter' => Constants::getArticleStatus(),
                        ],

                        [
                            'class' =>StatusColumn::className(),
                            'attribute' => 'recommend',
                            'filter' => Constants::getYesNoItems(),
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
                            'class' => ActionColumn::className(),
                            'buttons' => [
                                'entry' => function ($url, $model, $key) {
                                    return Html::a('课时', Url::to([
                                        'course-child/index',
                                        'CourseChildSearch[course_id]' => $model['id']
                                    ]), [
                                        'title' => $model['title'].'课时',
                                        'data-pjax' => '0',
                                        'class' => 'btn-sm J_menuItem openContab',
                                        'target' => '_blank',
                                    ]);
                                },
                                'password' => function ($url, $model, $key) {
                                    if ($model->price > 0){
                                        return Html::a('密码', Url::to([
                                            'course-password/index',
                                            'CoursePasswordSearch[course_id]' => $model['id']
                                        ]), [
                                            'title' => $model['title'].'密码',
                                            'data-pjax' => '0',
                                            'class' => 'btn-sm J_menuItem openContab',
                                            'target' => '_blank',
                                        ]);
                                    }
                                    return "";
                                }
                            ],
                            'template'=>'{entry} {password}'
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'template' => '{view-layer} {update} {delete} ',
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
<?php JsBlock::begin()?>
  <script>
    function showImg() {
      t = setTimeout(function () {
      }, 200);
      var url = $(this).attr('img');
      if (url.length === 0) {
        layer.tips('<?=Yii::t('app', 'No picture')?>', $(this));
      } else {
        layer.tips('<img style="max-width: 100px;max-height: 60px" src=' + url + '>', $(this));
      }
    }
    $(document).ready(function(){
      var t;
      $('table tr td a.thumbImg').hover(showImg,function(){
        clearTimeout(t);
      });
    });
    var container = $('#pjax');
    container.on('pjax:send',function(args){
      layer.load(2);
    });
    container.on('pjax:complete',function(args){
      layer.closeAll('loading');
      $('table tr td a.thumbImg').bind('mouseover mouseout', showImg);
      $("input.sort").bind('blur', indexSort);
      lay('.date-time').each(function(){
        var config = {
          elem: this,
          type: this.getAttribute('dateType'),
          range: this.getAttribute('range') === 'true' ? true : ( this.getAttribute('range') === 'false' ? false : this.getAttribute('range') ),
          format: this.getAttribute('format'),
          value: this.getAttribute('value') === 'new Date()' ? new Date() : this.getAttribute('value'),
          isInitValue: this.getAttribute('isInitValue') != 'false',
          min: this.getAttribute('min'),
          max: this.getAttribute('max'),
          trigger: this.getAttribute('trigger'),
          show: this.getAttribute('show') != 'false',
          position: this.getAttribute('position'),
          zIndex: parseInt(this.getAttribute('zIndex')),
          showBottom: this.getAttribute('showBottom') != 'false',
          btns: this.getAttribute('btns').replace(/\[/ig, '').replace(/\]/ig, '').replace(/'/ig,'').replace(/\s/ig, '').split(','),
          lang: this.getAttribute('lang'),
          theme: this.getAttribute('theme'),
          calendar: this.getAttribute('calendar') != 'false',
          mark: JSON.parse(this.getAttribute('mark'))
        };

        if( !this.getAttribute('search') ){//搜素
          config.done = function(value, date, endDate){
            setTimeout(function(){
              $(this).val(value);
              var e = $.Event("keydown");
              e.keyCode = 13;
              $(".date-time[search!='true']").trigger(e);
            },100)
          }
          delete config['value'];
        }

        laydate.render(config);
      });

    });
  </script>
<?php JsBlock::end()?>