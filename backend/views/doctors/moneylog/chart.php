<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/3/3
 * Time: 14:28
 */
use backend\widgets\ActiveForm;

$this->title = '图表查看';
$this->params['breadcrumbs'][] = '图表查看';
//$this->registerJsFile('@web/static/js/echarts-all.js',['position'=>\yii\web\View::EVENT_END_BODY]);
?>
<div class="row">
  <div class="col-sm-12">
    <div class="ibox">
        <?= $this->render('/widgets/_ibox-title') ?>
      <div class="ibox-content">

            <?php $form = ActiveForm::begin([
                'action' => ['chart'],
                'method' => 'post',
                'options'=>[
                    'class'=>'form-inline'
                ]
            ]); ?>

            <?if(!Yii::$app->user->identity->hospital_id):?>
                <div class="form-group">
                  <label for="exampleInputName2">选择医院</label>
                  <select name="data[hospital_id]" class="form-control">
                    <option value="">请选择</option>
                      <?foreach ($hospital as $k=>$item):?>
                        <option value="<?=$k?>" <?if ($k==$post['hospital_id']):?>selected<?endif;?>><?=$item?></option>
                      <?endforeach;?>
                  </select>
                </div>
            <?endif;?>
            <div class="form-group">
              <label for="exampleInputEmail2">查询时间</label>
              <input type="text" class="form-control" id="time" name="data[time]" placeholder="" value="<?=$post['time']?>">
            </div>
            <button type="submit" class="btn btn-success">搜索</button>
            <?php ActiveForm::end(); ?>

        <div style="margin-top: 20px;">
          <div id="main" style="height:600px;"></div>
          <script src="static/js/echarts-all.js"></script>
          <script>
            var myChart = echarts.init(document.getElementById('main'));
            var option = {
              title : {
                text: '<?=$json['title']?>',
                subtext: '<?=$post['time'] ?? ''?>',
                x:'center'
              },
              tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
              },
              legend: {
                orient : 'vertical',
                x : 'left',
                data:['收入','已提现','待提现',]
              },
              toolbox: {
                show : true,
                feature : {
                  mark : {show: true},
                  dataView : {show: true, readOnly: false},
                  magicType : {
                    show: true,
                    type: ['pie', 'funnel'],
                    option: {
                      funnel: {
                        x: '25%',
                        width: '50%',
                        funnelAlign: 'left',
                        max: 1548
                      }
                    }
                  },
                  restore : {show: true},
                  saveAsImage : {show: true}
                }
              },
              calculable : true,
              series : [
                {
                  name:'收支情况',
                  type:'pie',
                  radius : '55%',
                  center: ['50%', '60%'],
                  data:[
                    {value:<?=$json['add'] ?? 0?>, name:'收入'},
                    {value:<?=$json['reduce'] ?? 0?>, name:'已提现'},
                    {value:<?=$json['reduce_no'] ?? 0?>, name:'待提现'},
                  ]
                }
              ]
            };
            myChart.setOption(option);
          </script>
        </div>
      </div>
    </div>
  </div>
</div>
<?$this->beginBlock('laydate');?>
  laydate.render({
    elem: '#time',
    range: '~'
  });
<?$this->endBlock('laydate');?>
<?$this->registerJs($this->blocks['laydate'], \yii\web\View::POS_END); ?>
