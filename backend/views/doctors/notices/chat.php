<?php
$default = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PCEtLQpTb3VyY2UgVVJMOiBob2xkZXIuanMvNjR4NjQKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNjlkMmE1YWM4NiB0ZXh0IHsgZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE2OWQyYTVhYzg2Ij48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIxMy4xNzk2ODc1IiB5PSIzNi41Ij42NHg2NDwvdGV4dD48L2c+PC9nPjwvc3ZnPg==';
?>
<!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<div class="row">
  <div class="col-sm-12">
    <div class="ibox">
      <div class="ibox">
          <?= $this->render('/widgets/_ibox-title') ?>
        <div class="input-group" style="margin-top: 20px;">
          <input type="text" id="content" class="form-control" placeholder="" aria-describedby="basic-addon2">
          <span class="input-group-addon" id="send">发送</span>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="chatlist">
  <?if (!empty($data)):?>
    <?foreach ($data as $v):?>
      <?if ($v['from'] == $from):?>
          <div class="media">
            <div class="media-left">
              <a href="#">
                <img class="media-object"  alt="64x64"
                     src="<?=$my_avatar ?? $default?>"
                     data-holder-rendered="true" style="width: 64px; height: 64px;">
              </a>
            </div>
            <div class="media-body">
              <h5 class="media-heading"><?=date('Y-m-d H:i:s',$v['created_at'])?></h5>
                <p style="text-align: left;"><?=$v['content']?></p>
            </div>
          </div>
      <?else:?>
        <div class="media">
          <div class="media-body">
            <h5 class="media-heading" style="text-align: right;"><?=date('Y-m-d H:i:s',$v['created_at'])?></h5>
            <p style="text-align: right;"><?=$v['content']?></p>
          </div>
          <div class="media-right">
            <a href="#">
              <img class="media-object" alt="64x64"
                   src="<?=$ta_avatar ?? $default?>"
                   data-holder-rendered="true" style="width: 64px; height: 64px;">
            </a>
          </div>
        </div>
      <?endif;?>
    <?endforeach;?>
  <?endif;?>
</div>
<script>
    <?php $this->beginBlock('chat')?>
    var my_avatar = "<?=$my_avatar ?? $default?>";
    var ta_avatar = "<?=$ta_avatar ?? $default?>";
    var loading = false;
    var send_content = '';
      $('#send').on('click',function() {
        if (loading){
          alert('请勿重复提交');
          return false;
        }
        send_content = $('#content').val();
        loading = true;
        $.ajax({
          type:'post',
          dataType:'json',
          data:{content:send_content},
          success:function(data) {
            loading = false;
            if (data.code==1){
              $('#content').val('')
              var send_html = '';
              send_html +='  <div class="media">\n' +
                '    <div class="media-left">\n' +
                '      <a href="#">\n' +
                '        <img class="media-object"  alt="64x64"\n' +
                '             src="'+my_avatar+'"\n' +
                '             data-holder-rendered="true" style="width: 64px; height: 64px;">\n' +
                '      </a>\n' +
                '    </div>\n' +
                '    <div class="media-body">\n' +
                '      <h5 class="media-heading">'+data.time+'</h5>\n' + send_content +
                '    </div>\n' +
                '  </div>';
              $('#chatlist').append(send_html);
            } else {
              alert(data.msg);
            }
          }
        })
      })
    setInterval(function() {
      $.ajax({
        url:"<?= \yii\helpers\Url::to(['doctors/notices/get-chat','from'=>$to])?>",
        dataType:'json',
        success:function(data) {
          console.log(data);
          var html = '';
          for (i in data.data) {
            html += '  <div class="media">\n' +
              '    <div class="media-body">\n' +
              '      <h5 class="media-heading" style="text-align: right;">'+data.data[i].created_at+'</h5>\n' +data.data[i].content+
              '    </div>\n' +
              '    <div class="media-right">\n' +
              '      <a href="#">\n' +
              '        <img class="media-object" alt="64x64"\n' +
              '             src="'+ta_avatar+'"\n' +
              '             data-holder-rendered="true" style="width: 64px; height: 64px;">\n' +
              '      </a>\n' +
              '    </div>\n' +
              '  </div>';
          }
          $('#chatlist').append(html);
        }
      })
    },10000)
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


