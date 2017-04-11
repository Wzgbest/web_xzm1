<?php
namespace app\index\controller;

use think\Db;

class Index
{
    public function index()
    {
//        \think\Cache::clear();
        return 'index/index';
    }

    /**
     * 红包并发测试
     * http://webcall.app/index/index/curlTest/redid/8ce33936c7945d8b67ade6bb745ef7b5
     */
    public function curlTest()
    {
        config('db_config1.database',config('db_common_prefix').'sdzhongxun');
        $conn = Db::connect(config('db_config1'));
        $list = $conn->table(config('database.prefix').'employer')->field('telephone,system_token')->select();
        $red_id =input('param.redid');
        foreach ($list as $k=>$v) {
            $urls[] = 'http://192.168.0.110/huanxin/red_envelope/fetchRedEnvelope/userid/'.$v['telephone'].'/access_token/'.$v['system_token'].'/redid/'.$red_id;
        }
        dump($urls);
        $res = rolling_curl($urls,0);
        dump($res);
    }
}
