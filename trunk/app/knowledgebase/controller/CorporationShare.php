<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\knowledgebase\controller;

use app\huanxin\controller\User;

class CorporationShare{
    public function addShare(User $user){
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $chk_info = $user->checkUserAccess($userid,$access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }

        $result = ['status'=>0 ,'info'=>"发布动态时发生错误！"];
        $msg = input('param.msg');
        $imgs = input('param.img/a');
        if(!$msg || !$imgs){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $time = time();//input('param.time');
        $img_info_arr = [];
        foreach ($imgs as $img ){
            $img_info = get_app_img($img);
            if(!$img_info["status"]){
                $result['info'] = $img_info["message"];
                return json($result);
            }
            $img_info_arr[] = $img_info;
        }
        var_exp($msg,'$msg');
        var_exp($imgs,'$imgs');
        var_exp($img_info_arr,'$img_info_arr',1);

        $result['info'] = "发布动态功能开发中！";
        return json($result);
    }
    public function shareList(){}
    public function shareInfo(){}
    public function relayShare(){}
    public function addComment(){}
    public function like(){}
    public function tip(){}
}