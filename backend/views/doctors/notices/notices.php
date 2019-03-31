<?php

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\doctors\DoctorNotices */
/* @var $form backend\widgets\ActiveForm */
?>
<div class="row">
  <div class="col-sm-12">
    <div class="ibox">
        <?= $this->render('/widgets/_ibox-title') ?>
      <div class="ibox-content">
          <?php $form = ActiveForm::begin([
              'options' => [
                  'class' => 'form-horizontal'
              ],
              'method' => 'get',
              'id' => 'chat'
          ]); ?>
        <div class="hr-line-dashed"></div>
          <?= $form->field($model, 'to')->chosenSelect(\common\helpers\CommonHelpers::getChats())->label('发给谁') ?>
        <!--        <div class="hr-line-dashed"></div>-->
        <!--          --><? //= $form->field($model, 'content')->textInput(['maxlength' => true])->label('内容') ?>
        <div class="hr-line-dashed"></div>
        <div class="form-group">
          <div class="col-sm-4 col-sm-offset-2">
            <span class="btn btn-primary openContab" id="open" title="" href="">打开聊天窗口</span>
          </div>
        </div>
          <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
  <ul class="list-group">
      <? if (!empty($myMsg)): ?>
          <? foreach ($myMsg as $v): ?>
          <a href="<?=\yii\helpers\Url::to(['doctors/notices/chat','to'=>$v['from'],'refresh'=>1]);?>" class="openContab" title="与[<?= $v['name'] ?>]的通信">
            <li class="list-group-item">
              <span class="badge" style="color: red"><?= $v['count'] ?></span>
              与[<?= $v['name'] ?>]的通信
            </li>
          </a>
          <? endforeach; ?>
      <? endif; ?>
  </ul>
</div>
<script>
    <?php $this->beginBlock('chat')?>
    var to = '';
    var title = '';
    $('#doctorchats-to').on('change', function() {
      var url = "<?=\yii\helpers\Url::to(['doctors/notices/chat'])?>";
      to = $(this).val();
      console.log(to);
      url += "&to=" + to;
      title = $(this).find("option:selected").text();
      $('#open').attr('href', url);
      $('#open').attr('title', '与' + title + '的对话');
    })
    $('#open').on('click', function() {
      if (!to) {
        alert('请先选择对象');
        return false;
      }
    })
    <?php $this->endBlock()?>
</script>
<?php $this->registerJs($this->blocks['chat'], \yii\web\View::POS_LOAD); ?>
<!--<script src='//cdn.bootcss.com/socket.io/2.0.3/socket.io.js'></script>-->
<!--<script>-->
<!--  // 连接服务端-->
<!--  var socket = io('http://127.0.0.1:3120');-->
<!--  // 触发服务端的chat message事件-->
<!--  socket.emit('chat message', '这个是消息内容...');-->
<!--  // 服务端通过emit('chat message from server', $msg)触发客户端的chat message from server事件-->
<!--  socket.on('chat message from server', function(msg){-->
<!--    console.log('get message:' + msg + ' from server');-->
<!--  });-->
<!--</script>-->


