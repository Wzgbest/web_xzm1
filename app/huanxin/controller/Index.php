<?php
namespace app\huanxin\controller;

class Index
{
    public function index()
    {
        return 'index';
    }

    public function doJob()
    {
        $job ='huanxin/OverTimeRedEnvelope';
//        $data = '单模块的，且命名空间是app\job的，比如上面的例子';
        $userid = 2;
        $corp_id = 'sdzhongxun';

        $dat = [
            'userid'=>$userid,
            'corp_id'=>$corp_id,
            'red_data'=>''
        ];
        $data = json_encode($dat,true);
        $delay = 3600*24;
//        \think\Queue::push($job, $data, $queue = null);
        \think\Queue::later($delay, $job, $data, $queue = null);
//        两个方法，前者是立即执行，后者是在$delay秒后执行
    }

    public function workmanSocket()
    {

    }
}
