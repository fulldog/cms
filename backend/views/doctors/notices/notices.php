<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/3/23
 * Time: 23:28
 */
?>

<script src='//cdn.bootcss.com/socket.io/2.0.3/socket.io.js'></script>
<script>
  // 连接服务端
  var socket = io('http://127.0.0.1:3120');
  // 触发服务端的chat message事件
  socket.emit('chat message', '这个是消息内容...');
  // 服务端通过emit('chat message from server', $msg)触发客户端的chat message from server事件
  socket.on('chat message from server', function(msg){
    console.log('get message:' + msg + ' from server');
  });
</script>
