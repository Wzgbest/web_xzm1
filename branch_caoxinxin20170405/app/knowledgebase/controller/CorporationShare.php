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
        $imgs = request()->file('img');
        if(!$msg || !$imgs){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $path = ROOT_PATH . 'public' . DS . 'webroot' . DS . $chk_info["corp_id"] . DS . 'images';
        $infos = [];
        foreach($imgs as $img){
            $checkFlg = $img->check(["ext"=>config('upload_image.image_ext')]);
            if(!$checkFlg){
                return false;
            }
            $info = $img->move($path);
            if(!$info){
                exception("上传动态图片失败");
            }
            //var_exp($info,'$info');
            $savename = $info->getSaveName();
            $infos[] = $savename;
        }
        $share["userid"] = $chk_info['userinfo']['id'];
        $share["content"] = $msg;
        $share["create_time"] = time();
        $corporationShareModel = new CorporationShareModel($chk_info["corp_id"]);
        $corporationShareModel->link->startTrans();
        $share_id = $corporationShareModel->createCorporationShare($share);
        if(!$share_id){
            $corporationShareModel->link->rollback();
            exception("发布动态失败");
        }
        $share_pictures = [];
        $share_picture["share_id"] = $share_id;
        foreach ($infos as $info){
            $share_picture["path"] =  DS . 'webroot' . DS . $chk_info["corp_id"] . DS . 'images'.$info;
            $share_pictures[] = $share_picture;
        }
        $corporationSharePicture = new CorporationSharePicture($chk_info["corp_id"]);
        $share_pic_id = $corporationSharePicture->createMutipleCorporationSharePicture($share_pictures);
        if(!$share_pic_id){
            $corporationShareModel->link->rollback();
            exception("保存动态图片失败");
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