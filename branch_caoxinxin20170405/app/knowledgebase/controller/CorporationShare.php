<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\knowledgebase\controller;

use app\common\controller\Initialize;
use app\knowledgebase\model\CorporationShare as CorporationShareModel;
use app\knowledgebase\model\CorporationShareComment as CorporationShareCommentModel;
use app\knowledgebase\model\CorporationSharePicture;

class CorporationShare extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function addShare(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];

        $result = ['status'=>0 ,'info'=>"发布动态时发生错误！"];
        $msg = input('param.msg');
        if(!$msg){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $imgs = request()->file('img');
        $img_num = 9;
        if(count($imgs)>$img_num){
            exception("上传动态图片不能大于".$img_num."张!");
        }
        //trace(var_exp($imgs,'$imgs','return'));
        $infos = [];
        if($imgs) {
            $path = ROOT_PATH . 'public' . DS . 'webroot' . DS . $this->corp_id . DS . 'images';
            foreach($imgs as $img){
                $checkFlg = $img->check(["ext"=>config('upload_image.image_ext')]);
                //trace(var_exp($img,'$img','return'));
                if(!$checkFlg){
                    exception("上传动态图片检查失败");
                }
                $info = $img->move($path);
                //trace(var_exp($info,'$info','return'));
                if($info===false){
                    exception("上传动态图片失败");
                }
                //var_exp($info,'$info');
                $savename = $info->getSaveName();
                $infos[] = $savename;
            }
        }
        //trace(var_exp($infos,'$infos','return'));
        $share["userid"] = $uid;
        $share["content"] = $msg;
        $share["create_time"] = time();
        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $corporationShareModel->link->startTrans();
        $share_id = $corporationShareModel->createCorporationShare($share);
        //trace(var_exp($share_id,'$share_id','return'));
        if(!$share_id){
            $corporationShareModel->link->rollback();
            exception("发布动态失败");
        }
        if($infos){
            $share_pictures = [];
            $share_picture["share_id"] = $share_id;
            $url_path = DS . 'webroot' . DS . $this->corp_id . DS . 'images' . DS;
            foreach ($infos as $info){
                $share_picture["path"] = $url_path . $info;
                $share_pictures[] = $share_picture;
            }
            //trace(var_exp($share_pictures,'$share_pictures','return'));
            $corporationSharePicture = new CorporationSharePicture($this->corp_id);
            $corporationSharePicture->createMutipleCorporationSharePicture($share_pictures);
        }
        $corporationShareModel->link->commit();
        $result['data'] = $share_id;
        $result['status'] = 1;
        $result['info'] = "发布成功！";
        return json($result);
    }
    public function shareList(){
        $result = ['status'=>0 ,'info'=>"获取动态时发生错误！"];
        $num = input('num',10,'int');
        $last_id = input("last_id",0,"int");
        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $share_data = $corporationShareModel->getCorporationShare($num,$last_id);
        $share_ids = array_column($share_data,"id");
        $corporationShareCommentModel = new CorporationShareCommentModel($this->corp_id);
        $share_comment_data = $corporationShareCommentModel->getCorporationShareComment($share_ids,$num,$last_id);
        $share_comment_Index = [];
        foreach ($share_comment_data as $share_comment){
            $share_comment_Index[$share_comment["share_id"]][] = $share_comment;
        }
        foreach ($share_data as &$share){
            if(isset($share_comment_Index[$share["id"]])){
                $share["comments"] = $share_comment_Index[$share["id"]];
            }
        }
        $result['data'] = $share_data;
        $result['status'] = 1;
        $result['info'] = "获取成功！";
        return json($result);
    }
    public function shareInfo(){}
    public function relayShare(){}
    public function addComment(){}
    public function like(){}
    public function tip(){}
}