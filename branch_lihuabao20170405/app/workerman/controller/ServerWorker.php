<?php
/**
 * Created by messhair
 * Date: 17-3-27
 * 提供系统客户端链接
 */
namespace app\workerman\controller;

use think\worker\Server;
use Workerman\Worker;
use app\workerman\controller\HandleMessage;

class ServerWorker extends Server
{
    protected $socket = 'websocket://webcall.app:8001';
//    protected $socket = 'tcp://0.0.0.0:8001';
    protected $processes =2;
    protected $name = 'workman_server';
    protected $handle;

    public function __construct()
    {
//        Worker::$daemonize =true;
        Worker::$pidFile = RUNTIME_PATH.'temp/workman.pid';
//        Worker::$stdoutFile = RUNTIME_PATH.'temp/workmandump';
        Worker::$logFile = RUNTIME_PATH.'temp/workman.log';
        $this->handle = new HandleMessage();
        parent::__construct();
    }
    /**
     * find the processid
     * @param $connection
     */
    public function onWorkerStart($connection)
    {
        $data = ['process_id'=>$connection->id];
        echo json_encode($data);
    }

    /**
     * establish connection
     * @param $connection
     */
    public function onConnect($connection)
    {
        $data = [
            'user'=>'you are invited',
            'time'=>date('Y-m-d H:i:s',time()),
            'process_id'=>$connection->id
        ];
        $connection->send(json_encode($data));
    }

    /**
     * receive message and response
     * @param $connection    the connection obj
     * @param $data    the message from client
     */
    public function onMessage($connection,$data)
    {
        $res = $this->handle->recordCall($data);
        $connection->send(json_encode($res));
    }

    public function onError($connection,$data)
    {
        echo 'error occurred';
    }

    public function onClose($connection,$data)
    {
        echo 'connection closed';
    }
}