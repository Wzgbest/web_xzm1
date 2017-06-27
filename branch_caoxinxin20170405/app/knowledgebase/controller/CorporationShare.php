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
        if(!$msg){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $imgs = request()->file('img');
        trace(var_exp($imgs,'$imgs','return'));
        $infos = [];
        if($imgs) {
            $path = ROOT_PATH . 'public' . DS . 'webroot' . DS . $chk_info["corp_id"] . DS . 'images';
            foreach($imgs as $img){
                $checkFlg = $img->check(["ext"=>config('upload_image.image_ext')]);
                trace(var_exp($img,'$img','return'));
                if(!$checkFlg){
                    exception("上传动态图片检查失败");
                }
                $info = $img->move($path);
                trace(var_exp($info,'$info','return'));
                if($info===false){
                    exception("上传动态图片失败");
                }
                //var_exp($info,'$info');
                $savename = $info->getSaveName();
                $infos[] = $savename;
            }
        }
        trace(var_exp($infos,'$infos','return'));
        $share["userid"] = $chk_info['userinfo']['id'];
        $share["content"] = $msg;
        $share["create_time"] = time();
        $corporationShareModel = new CorporationShareModel($chk_info["corp_id"]);
        $corporationShareModel->link->startTrans();
        $share_id = $corporationShareModel->createCorporationShare($share);
        trace(var_exp($share_id,'$share_id','return'));
        if(!$share_id){
            $corporationShareModel->link->rollback();
            exception("发布动态失败");
        }
        if($infos){
            $share_pictures = [];
            $share_picture["share_id"] = $share_id;
            $url_path = DS . 'webroot' . DS . $chk_info["corp_id"] . DS . 'images' . DS;
            foreach ($infos as $info){
                $share_picture["path"] = $url_path . $info;
                $share_pictures[] = $share_picture;
            }
            trace(var_exp($share_pictures,'$share_pictures','return'));
            $corporationSharePicture = new CorporationSharePicture($chk_info["corp_id"]);
            $corporationSharePicture->createMutipleCorporationSharePicture($share_pictures);
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