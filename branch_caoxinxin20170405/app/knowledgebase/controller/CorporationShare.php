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
use app\knowledgebase\model\CorporationShareContent;
use app\knowledgebase\model\CorporationSharePicture;
use app\knowledgebase\model\CorporationShareLike;
use app\knowledgebase\model\CorporationShareTip;
use app\common\model\Employee;
use app\huanxin\model\TakeCash;

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
        $msg = input('msg',"","string");
        if(empty($msg)){
            exception("参数错误!");
        }
        $imgs = request()->file('img');
        $img_num_max = 9;
        if(count($imgs)>$img_num_max){
            exception("上传动态图片不能大于".$img_num_max."张!");
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
        $share["create_time"] = time();
        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $corporationShareContentModel = new CorporationShareContent($this->corp_id);
        $corporationShareModel->link->startTrans();
        $content["content"] = $msg;
        $content_id = $corporationShareContentModel->createCorporationShareContent($content);
        if(!$content_id){
            exception("发布动态内容失败");
        }
        $share["content_id"] = $content_id;
        $share_id = $corporationShareModel->createCorporationShare($share);
        //trace(var_exp($share_id,'$share_id','return'));
        if(!$share_id){
            $corporationShareModel->link->rollback();
            exception("发布动态失败");
        }
        if($infos){
            $share_pictures = [];
            $share_picture["content_id"] = $content_id;
            $url_path = DS . 'webroot' . DS . $this->corp_id . DS . 'images' . DS;
            foreach ($infos as $info){
                $share_picture["path"] = $url_path . $info;
                $share_pictures[] = $share_picture;
            }
            //trace(var_exp($share_pictures,'$share_pictures','return'));
            $corporationSharePicture = new CorporationSharePicture($this->corp_id);
            $res = $corporationSharePicture->createMutipleCorporationSharePicture($share_pictures);
            if(!$res["res"]){
                $corporationShareModel->link->rollback();
                exception("上传动态图片失败");
            }
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
        $userinfo = get_userinfo();
        //var_exp($userinfo,'$userinfo',1);
        $uid = $userinfo["userid"];
        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $share_data = $corporationShareModel->getCorporationShare($uid,$num,$last_id);
        $share_ids = array_column($share_data,"id");
        $corporationShareCommentModel = new CorporationShareCommentModel($this->corp_id);
        $share_comment_data = $corporationShareCommentModel->getAllCorporationShareComment($share_ids);
        $share_comment_Index = [];
        foreach ($share_comment_data as $share_comment){
            //$share_comment["reply_content"] = utf8_decode($share_comment["reply_content"]);
            $share_comment_Index[$share_comment["share_id"]][] = $share_comment;
        }
        foreach ($share_data as &$share){
            if(isset($share_comment_Index[$share["id"]])){
                $share["comment_list"] = $share_comment_Index[$share["id"]];
            }else{
                $share["comment_list"] = [];
            }
        }
        $result['data'] = $share_data;
        $result['status'] = 1;
        $result['info'] = "获取成功！";
        return json($result);
    }
    public function shareInfo(){}
    public function relayShare(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result = ['status'=>0 ,'info'=>"获取动态时发生错误！"];
        $share_id = input('share_id',10,'int');
        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $share_data = $corporationShareModel->getCorporationShareById($share_id);
        $new_share_id = $corporationShareModel->relayCorporationShare($share_id,$uid);
        $result['data'] = $new_share_id;
        $result['status'] = 1;
        $result['info'] = "获取成功！";

         //发送评论消息
        $userinfos = $userinfo['userinfo'];
        $str = $userinfos['truename']."转发了你发表的内容";
        $receive_uids[] = $share_data['userid'];
        save_msg($str,"/knowledgebase/speech_craft/show/id/".$share_id,$receive_uids,5,12,$uid,$share_id);

        return json($result);
    }
    public function addComment(){
        $result = ['status'=>0 ,'info'=>"评论动态时发生错误！"];
        $share_id = input('share_id',0,"int");
        $reply_content = input('reply_content',"","string");
        //$reply_content = utf8_encode($reply_content);
        //var_exp($reply_content,'$reply_content',1);
        if(empty($share_id) || empty($reply_content)){
            exception("参数错误!");
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $replyer_id = $uid;
        $comment_id = input('comment_id',0,"int");
        $reviewer_id = 0;
        $corporationShareCommentModel = new CorporationShareCommentModel($this->corp_id);
        if($comment_id){
            $reply_comment= $corporationShareCommentModel->getOneComment($comment_id);
            $reviewer_id = $reply_comment["replyer_id"];
        }
        $comment["share_id"] = $share_id;
        $comment["replyer_id"] = $replyer_id;
        $comment["reply_content"] = $reply_content;
        $comment["reviewer_id"] = $reviewer_id;
        $comment["reply_comment_id"] = $comment_id;
        $comment["comment_time"] = time();
        $add_comment_flg= $corporationShareCommentModel->createCorporationShareComment($comment);
        $result['data'] = $add_comment_flg;
        $result['status'] = 1;
        $result['info'] = "评论成功！";

        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $share_data = $corporationShareModel->getCorporationShareById($share_id);
        //发送评论消息
        $userinfos = $userinfo['userinfo'];
        $str = $userinfos['truename']."评论了你发表的动态";
        $receive_uids[] = $share_data['userid'];
        $sms['img_url'] = "/message/images/pinglun.png";
        save_msg($str,"/knowledgebase/corporation_share/index",$receive_uids,5,13,$uid,$sms);
        if ($comment_id) {
            save_msg($userinfos['truename']."回复了你的评论","/knowledgebase/corporation_share/index",[$reviewer_id],5,13,$uid,$sms);
        }

        return json($result);
    }
    public function like(){
        $result = ['status'=>0 ,'info'=>"喜欢动态时发生错误！"];
        $share_id = input('share_id',0,"int");
        $not_like = input('not_like',0,"int");
        if(empty($share_id)){
            exception("参数错误!");
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];

        $LikeModel = new CorporationShareLike($this->corp_id);
        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $old_flg = false;
        $flg = false;
        $like_info = $LikeModel->getlike($uid,$share_id);
        if($not_like==0){
            if(!empty($like_info)){
                $old_flg = true;
            }else{
                $flg = $LikeModel->like($uid,$share_id);

                //发送点赞消息
                $share_data = $corporationShareModel->getCorporationShareById($share_id);
                $userinfos = $userinfo['userinfo'];
                $str = $userinfos['truename']."点赞了你发布的动态";
                $receive_uids[] = $share_data['userid'];
                $sms['img_url'] = "/message/images/dianzan.png";
                save_msg($str,"/knowledgebase/corporation_share/index",$receive_uids,5,13,$uid,$sms);
            }
        }else{
            if(empty($like_info)){
                $old_flg = true;
            }else{
                $flg = $LikeModel->not_like($uid,$share_id);
            }
        }
        if($old_flg){
            $result['status'] = 1;
            $result['info'] = "已经";
            $result['info'] .= ($not_like?"不":"");
            $result['info'] .= "喜欢这条动态了！";
        }elseif($flg){
            $result['status'] = 1;
            $result['info'] = "喜欢动态成功！";
            $result['info'] = ($not_like?"不":"").$result['info'];
        }
        
        $share_data = $corporationShareModel->getCorporationShareById($share_id);
        $result['data'] = $share_data["good_count"];
        return json($result);
    }
    public function tip(){
        $result = ['status'=>0 ,'info'=>"打赏动态时发生错误！"];
        $share_id = input('share_id',0,"int");
        $money = 0+input('money');
        $paypassword = input('paypassword');
        if(empty($share_id)||empty($money)||empty($paypassword)){
            $result['info'] = '参数错误';
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        if (md5($paypassword) != $userinfo['userinfo']['pay_password']) {
            $result['info'] = '支付密码错误';
            $result['status'] = 6;
            return json($result);
        }
        $save_money = intval($money*100);
        $time = time();
        if ($userinfo['userinfo']['left_money'] < $save_money) {
            $info['info'] = '账户余额不足';
            $info['status'] = 5;
            return json($info);
        }

        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $TipModel = new CorporationShareTip($this->corp_id);
        $employM = new Employee($this->corp_id);
        $cashM = new TakeCash($this->corp_id);
        $share_data = $corporationShareModel->getCorporationShareById($share_id);
        if(empty($share_data)){
            $result['info'] = '未找到动态';
            return json($result);
        }
        $flg = false;
        $TipModel->link->startTrans();
        try{
            $flg = $TipModel->tip($uid,$share_id,$money);
            if (!$flg) {
                exception("添加打赏记录发生错误!");
            }
            $tip_from_user = $employM->setEmployeeSingleInfo($userinfo["telephone"],['left_money'=>['exp',"left_money - $save_money"]],["left_money"=>["egt",$save_money]]);
            if (!$tip_from_user) {
                exception("更新打赏用户余额发生错误!");
            }
            $order_data = [
                'userid'=>$userinfo['userinfo']['id'],
                "take_type"=>6,
                "take_type_sub"=>1,
                "take_id"=>$share_id,
                'take_money'=> -$save_money,
                'take_status'=>1,
                'took_time'=>$time,
                'remark' => '打赏工作圈',
                "status"=>1
            ];
            $tip_from_cash_rec = $cashM->addOrderNumber($order_data);
            if (!$tip_from_cash_rec) {
                exception("添加打赏交易记录发生错误!");
            }

            $tip_to_user = $employM->setEmployeeSingleInfo($share_data["telephone"],['left_money'=>['exp',"left_money + $save_money"]]);
            if (!$tip_to_user) {
                exception("更新被打赏用户余额发生错误!");
            }
            $order_data = [
                'userid'=>$share_data["userid"],
                "take_type"=>6,
                "take_type_sub"=>1,
                "take_id"=>$share_id,
                'take_money'=> $save_money,
                'take_status'=>1,
                'took_time'=>$time,
                'remark' => '工作圈打赏',
                "status"=>1
            ];
            $tip_to_cash_rec = $cashM->addOrderNumber($order_data);
            if (!$tip_to_cash_rec) {
                exception("添加被打赏交易记录发生错误!");
            }
            $TipModel->link->commit();
        }catch(\Exception $ex){
            $TipModel->link->rollback();
            $result['info'] = '打赏失败';
            return json($result);
        }

        //发送打赏消息
        $userinfos = $userinfo["userinfo"];
        $str = $userinfos['truename']."打赏了你的动态，赏金".$money."元";
        $receive_uids[] = $share_data['userid'];
        $sms['img_url'] = "/message/images/dashang.png";
        save_msg($str,"/knowledgebase/corporation_share/index",$receive_uids,5,13,$uid,$sms);

        $telphone = $userinfo["telephone"];
        $userinfo = $employM->getEmployeeByTel($telphone);
        set_userinfo($this->corp_id,$telphone,$userinfo);
        
        $share_data = $corporationShareModel->getCorporationShareById($share_id);
        
        $tipEmployeeList = $TipModel->getTipList($share_id);
        $myTipMoney = $TipModel->getMyTipMoney($uid,$share_id);
        $result['info'] = '打赏成功';
        $result['status'] = 1;
        $result['data']["rewards"] = $share_data["rewards"];
        $result['data']["my_tip"] = $myTipMoney;
        $result['data']["tip_list"] = $tipEmployeeList;
        return json($result);
    }
    public function tip_list(){
        $result = ['status'=>0 ,'info'=>"获取动态打赏列表时发生错误！"];
        $share_id = input('share_id',0,"int");
        if(empty($share_id)){
            $result['info'] = '参数错误';
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $TipModel = new CorporationShareTip($this->corp_id);
        $tipEmployeeList = $TipModel->getTipList($share_id);
        $myTipMoney = $TipModel->getMyTipMoney($uid,$share_id);
        $result['data']["my_tip"] = $myTipMoney;
        $result['data']["tip_list"] = $tipEmployeeList;
        $result['info'] = '获取动态打赏列表成功';
        $result['status'] = 1;
        return json($result);
    }
    public function my_tip(){
        $result = ['status'=>0 ,'info'=>"获取动态打赏列表时发生错误！"];
        $share_id = input('share_id',0,"int");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $TipModel = new CorporationShareTip($this->corp_id);
        $tipEmployeeList = $TipModel->getMyTip($uid,$share_id);
        $result['data'] = $tipEmployeeList;
        $result['info'] = '获取我的动态打赏列表成功';
        $result['status'] = 1;
        return json($result);
    }

    public function delete_share(){
        $result = ['status'=>0,'info'=>"删除动态失败!"];

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $share_id = input('share_id',0,'int');
        if (!$share_id) {
            $result['info'] = "动态id为0!";
            return json($result);
        }

        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $del_flag = $corporationShareModel->delOneShareById($share_id,$uid);
        if ($del_flag) {
            $result['data'] = $del_flag;
            $result['status'] = 1;
            $result['info'] = "删除成功!";
        }

        return json($result);
    }

    public function delete_comment(){
        $result = ['status'=>0,'info'=>"删除评论失败!"];

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $comment_id = input('comment_id',0,'int');
        if (!$comment_id) {
            $result['info'] = "评论id不能为空";
            return json($result);
        }

        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $del_flag = $corporationShareModel->delOneCommentById($comment_id,$uid);
        if ($del_flag) {
            $result['data'] = $del_flag;
            $result['status'] = 1;
            $result['info'] = "删除成功!";
        }
        
        return json($result);
    }

    public function index(){

        $num = input('num',100,'int');
        $last_id = input("last_id",0,"int");

        $key_word = input('key_word','','string');
        // var_dump($key_word);die();
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $corporationShareModel = new CorporationShareModel($this->corp_id);
        $share_data = $corporationShareModel->getCorporationShare($uid,$num,$last_id,$key_word);
        $share_ids = array_column($share_data,"id");
        $corporationShareCommentModel = new CorporationShareCommentModel($this->corp_id);
        $share_comment_data = $corporationShareCommentModel->getAllCorporationShareComment($share_ids);
        $share_comment_Index = [];
        foreach ($share_comment_data as $share_comment){
            //$share_comment["reply_content"] = utf8_decode($share_comment["reply_content"]);
            $share_comment_Index[$share_comment["share_id"]][] = $share_comment;
        }
        foreach ($share_data as &$share){
            if(isset($share_comment_Index[$share["id"]])){
                $share["comment_list"] = $share_comment_Index[$share["id"]];
            }else{
                $share["comment_list"] = [];
            }
        }
        // var_dump($share_data);die();
        $this->assign('share_list',$share_data);
        $this->assign('userinfo',$userinfo);
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }

    public function add_share_page(){
        return view('new');
    }

    public function reward(){
        $share_id = input('share_id',0,'int');
        // var_dump($share_id);die();
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $TipModel = new CorporationShareTip($this->corp_id);
        $tipEmployeeList = $TipModel->getTipList($share_id);
        $myTipMoney = $TipModel->getMyTipMoney($uid,$share_id);
        $money = array_column($tipEmployeeList,'money');
        $tip_total = array_sum($money);
        $data["my_tip"] = $myTipMoney;
        $data["tip_list"] = $tipEmployeeList;
        $data['tip_total'] = $tip_total;
        // var_dump($data);die();
        $this->assign('uid',$uid);
        $this->assign('data',$data);
        return view();
    }

    public function pay(){
        $money = input('money',0,'int');
        if (!$money) {
            $this->error("输入的金额有误!");
        }
        $userinfo = get_userinfo();
        $this->assign('user_money',$userinfo["userinfo"]['left_money']/100);
        $this->assign('money',$money);
        return view();
    }
}