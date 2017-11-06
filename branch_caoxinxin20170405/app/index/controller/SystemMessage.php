<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\common\controller\Initialize;
use app\huanxin\service\Api as HuanxinApi;

class SystemMessage extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }
    public function get_msg_list(){

    }
    public function get_msg(){

    }
    public function add_msg(){
        $huanxin = new HuanxinApi();
        $huanxin_flg = $huanxin->sendMessage(
            "users",
            ["sdzhongxun_5"],
            "消息测试",
            "txt",
            "",
            [
                "message_id"=>1,
                "message_is_read"=>0,
                "message_type"=>1,
            ]
        );
    }
    public function del_msg(){

    }
    public function push_msg(){

    }
    public function set_read_msg(){
        $huanxin = new HuanxinApi();
        $huanxin_flg = $huanxin->sendMessage(
            "users",
            ["sdzhongxun_5"],
            "消息已读",
            "cmd",
            "",
            [
                "message_id"=>1,
                "message_is_read"=>1
            ]
        );
    }
    public function set_read_msg_by_type(){

    }
    public function set_read_msg_all(){

    }
}
