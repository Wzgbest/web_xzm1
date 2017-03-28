<?php
namespace app\workerman\controller;
use Workerman\Worker;

class Index
{
    public function index()
    {
        return view();
    }

    /**
     * start:   php index.php Index/server start
     */
    public function server()
    {
        if (!IS_CLI) {
            die("access illegal");
        }

        define('MAX_REQUEST', 1000);

        Worker::$daemonize = true;
        Worker::$pidFile = RUNTIME_PATH.'temp/workerman.pid';
        Worker::$stdoutFile = RUNTIME_PATH.'log/workmandump.log';//输出日志, 如echo，var_dump等
        Worker::$logFile = RUNTIME_PATH.'log/workerman.log';//workerman自身相关的日志，包括启动、停止等,不包含任何业务日志

        $worker = new Worker('websocket://webcall.app');
        $worker->name = 'workman_';
        $worker->count = 2;
//        $worker->transport = 'udp';// 使用udp协议，默认TCP

        $worker->onWorkerStart = function ($worker) {
            echo "Worker starting...\n";
        };
        $worker->onMessage = function ($connection, $data) {
            static $request_count = 0;// 已经处理请求数
            var_dump($data);
            $connection->send("hello");

            /*
            * 退出当前进程，主进程会立刻重新启动一个全新进程补充上来，从而完成进程重启
            */
            if (++$request_count >= MAX_REQUEST) {// 如果请求数达到1000
                Worker::stopAll();
            }
        };


        if(++$request_count >= MAX_REQUEST){// 如果请求数达到1000
            Worker::stopAll();
        }

        $worker->onBufferFull = function($connection){
            echo "bufferFull and do not send again\n";
        };
        $worker->onBufferDrain = function($connection){
            echo "buffer drain and continue send\n";
        };

        $worker->onWorkerStop = function($worker){
            echo "Worker stopping...\n";
        };

        $worker->onerror = function($connection, $code, $msg){
            echo "error $code $msg\n";
        };

        // 运行worker
        Worker::runAll();
    }
}
