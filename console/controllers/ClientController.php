<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/3/23
 * Time: 18:02
 */

namespace console\controllers;


use PHPSocketIO\SocketIO;
use Workerman\Worker;
use yii\console\Controller;

class ClientController extends Controller
{

    /**
     * client
     */
    function actionIndex()
    {
        $io = new SocketIO(3120);
        $io->origins('http://k.liufeecms.cn');
    // 监听一个http端口，通过http协议访问这个端口可以向所有客户端推送数据(url类似http://ip:9191?msg=xxxx)
        $io->on('workerStart', function () use ($io) {
            $inner_http_worker = new Worker('http://0.0.0.0:9191');
            $inner_http_worker->onMessage = function ($http_connection, $data) use ($io) {
                if (!isset($_GET['msg'])) {
                    return $http_connection->send('fail, $_GET["msg"] not found');
                }
                $io->emit('chat message', $_GET['msg']);
                $http_connection->send('ok111');
            };
            $inner_http_worker->listen();
        });

    // 当有客户端连接时
        $io->on('connection', function ($socket) use ($io) {
            // 定义chat message事件回调函数
            $socket->on('chat message', function ($msg) use ($io) {
                // 触发所有客户端定义的chat message from server事件
                $io->emit('chat message from server', $msg);
            });
        });

        Worker::runAll();
    }
}