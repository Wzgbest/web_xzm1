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
use app\knowledgebase\model\CorporationShare as CorporationShareModel;
use app\knowledgebase\model\CorporationSharePicture;

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
        $img = input('param.img');
        $img_file = request()->file('file');
        trace("share_test_file:");
        trace(base64_encode($img_file));
        if(!$msg || !$img){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $img_info = get_app_img($img);
        if(!$img_info["status"]){
            exception($img_info["message"]);
        }
        $share["userid"] = $chk_info['userinfo']['id'];
        $share["content"] = $msg;
        $share["create_time"] = time();
        trace(json_encode($share));
        $corporationShareModel = new CorporationShareModel($chk_info["corp_id"]);
        $corporationShareModel->link->startTrans();
        $share_id = $corporationShareModel->createCorporationShare($share);
        if(!$share_id){
            $corporationShareModel->link->rollback();
            exception("发布动态失败");
        }
        $share_picture["share_id"] = $share_id;
        $share_picture["path"] = $img;
        trace(json_encode($share_picture));
        $corporationSharePicture = new CorporationSharePicture($chk_info["corp_id"]);
        $share_pic_id = $corporationSharePicture->createCorporationSharePicture($share_picture);
        if(!$share_pic_id){
            $corporationShareModel->link->rollback();
            exception("上传动态图片失败");
        }
        $corporationShareModel->link->commit();
        $result['data'] = $share_id;
        $result['status'] = 1;
        $result['info'] = "发布成功！";
        return json($result);
    }
    public function shareList(){}
    public function shareInfo(){}
    public function relayShare(){}
    public function addComment(){}
    public function like(){}
    public function tip(){}
}