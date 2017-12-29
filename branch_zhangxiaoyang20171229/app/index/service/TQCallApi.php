<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\index\service;

class TQCallApi{
    private $tq_config = [];
    private $get_access_token = "http://vip.agent.tq.cn/vip/open/auth/token";
    public function __construct(){
        $this->tq_config = config('tq');
    }
    public function get_access_token($admin_uin,$uin,$strid,$time){
        $appid = $this->tq_config["appid"];
        $appkey = $this->tq_config["appkey"];
        $secretkey = strtoupper(md5($appid."*(**)*".$appkey));
        $md5_str = $admin_uin . '&' . $uin . '&'. $strid . '&' . $appid . '&' . $secretkey . '&' . $time;
//        var_exp($md5_str,'$md5_str');
        $sign = strtoupper(md5($md5_str));
        $param["admin_uin"] = $admin_uin;
        $param["uin"] = $uin;
        $param["strid"] = $strid;
        $param["appid"] = $appid;
        $param["ct"] = $time;
        $param["sign"] = $sign;
        $param_str = http_build_query($param);
//        var_exp($param_str,'$param_str');
        $get_access_token_url = $this->get_access_token."?".$param_str;
        $access_token_data = curl_get($get_access_token_url);
        $access_token_arr = json_decode($access_token_data,true);
        return $access_token_arr;
    }
}