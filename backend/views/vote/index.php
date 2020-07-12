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
/* @var $searchModel common\models\VoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label'=>yii::t('app',  '投票活动')];

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

//                        'id',
                        'title',
                        [
                            'attribute' => 'img',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                if ($model->img == '') {
                                    $num = Constants::YesNo_No;
                                } else {
                                    $num = Constants::YesNo_Yes;
                                }
                                return Html::a(Constants::getYesNoItems($num), $model->img ? $model->img : 'javascript:void(0)', [
                                    'img' => $model->img ? $model->img : '',
                                    'class' => 'thumbImg',
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]);
                            },
                            'filter' => false,
                        ],
                        [
                            'filter'=>false,
                            'attribute'=>'vote_count'
                        ],
                        [
                            'filter'=>false,
                            'attribute'=>'pv'
                        ],
//                        'desc',
                        [
                            'class' =>StatusColumn::className(),
                            'attribute' => 'recommend',
                            'filter' => Constants::getYesNoItems(),
                        ],
                        [
                            'filter'=>false,
                            'attribute'=>'start_time',
                        ],
                        [
                            'filter'=>false,
                            'attribute'=>'end_time'
                        ],
//                        'start_time:datetime',
//                        'end_time:datetime',

                        // 'updated_at',
                        // 'created_at',

                        [
                            'class' => ActionColumn::className(),
                            'buttons' => [
                                'entry' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa fa-bars" aria-hidden="true"></i>', Url::to([
                                        'vote-child/index',
                                        'VoteChildSearch[vid]' => $model['id']
                                    ]), [
                                        'title' => $model['title'].'详情',
                                        'data-pjax' => '0',
                                        'class' => 'btn-sm J_menuItem ',//openContab
//                                        'target' => '_blank',
                                    ]);
                                },
                            ],
                            'template' => '{entry} {view-layer} {update} {delete} ',
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