<?php
//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("0.0.0.0", 9502);
$ws->user_c = [];   //给ws对象添加属性user_c，值为空数组；
//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    $ws->user_c[] = $request->fd;
    //$ws->push($request->fd, "hello, welcome\n");
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $msg =  'from'.$frame->fd.":{$frame->data}\n";
   foreach($ws->user_c as $v){
      $ws->push($v,$msg);
  }
   // $ws->push($frame->fd, "server: {$frame->data}");
    // $ws->push($frame->fd, "server: {$frame->data}");
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    //删除已断开的客户端
    unset($ws->user_c[$fd-1]);
});

$ws->start();
