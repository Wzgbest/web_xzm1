<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuzhenguo <wuzhenguook@gmail.com> <The Best Word!!!>
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\common\controller\Initialize;
use app\huanxin\service\Api as HuanxinApi;
use app\index\model\SystemMessage as SystemMessageModel;

class SystemMessage extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 保存发送消息
     * @param  string  $msg          消息
     * @param  string  $url          链接
     * @param  integer $type         消息类型1系统消息 2用户消息 3任务消息 4CRM 5知识库
     * @param  integer $send_uid     发送人id
     * @param  array   $receive_uids 接收人id 数组
     * @param  integer $to_instation 是否发送站内信
     * @param  integer $to_app       是否发送app
     * @param  integer $to_pc        是否发送pc
     * @param  integer $to_email     是否发送email
     * @param  integer $to_sms       是否发送短信
     * @return [type]                [description]
     */
    public function save_msg($msg='',$url='',$receive_uids=[],$type=1,$send_uid=0,$to_instation=1,$to_app=1,$to_pc=1,$to_email=0,$to_sms=0){
        $info = ['status'=>0,'message'=>"消息发送失败"];

        if (empty($receive_uids) || !$msg) {
            $info['message'] = "不能没有接受人或者发送信息为空";
            return json($info);
        }

        $msg_data['type'] = $type;
        $msg_data['send_uid'] = $send_uid;
        $msg_data['to_instation'] = $to_instation;
        $msg_data['to_app'] = $to_app;
        $msg_data['to_pc'] = $to_pc;
        $msg_data['to_email'] = $to_email;
        $msg_data['to_sms'] = $to_sms;
        $msg_data['msg'] = $msg;
        $msg_data['url'] = $url;
        $msg_data['create_time'] = time();
        $msg_data['status'] = 1;

        if ($send_uid == 0) {
            $from = "系统消息";
        }else{
            $from = "admin";
        }

        $systemM = new SystemMessageModel($this->corp_id);
        $systemM->link->startTrans();
        try {
            $msg_id = $systemM->addMsg($msg_data);
            if (!$msg_id) {
                $info['error'] = "插入消息数据表失败";
                exception("插入消息数据表失败");
            }
            foreach ($receive_uids as $key => $value) {
                $msg_link_data[$key]['msg_id'] = $msg_id;
                $msg_link_data[$key]['receive_uid'] = $value;
                $msg_link_data[$key]['create_time'] = 0;
                $msg_link_data[$key]['status'] = 0;
                $target[] = $this->corp_id."_".$value;
            }
            $flg = $systemM->addMsgLink($msg_link_data);
            if (!$flg) {
                $info['error'] = "插入消息连接表失败";
                exception("插入消息连接表失败");
            }

            if ($to_app == 1 || $to_pc == 1) {
                $flg = $this->add_msg($from,$target,$msg_id,$msg,$url,$type,$to_app,$to_pc);
                if ($flg['status'] == 0) {
                    $info['error'] = "发送信息出现错误";
                    exception("发送信息出现错误");
                }
            }
            if ($to_email == 1) {
                //发送邮件
            }
            if ($to_sms == 1) {
                //发送短信
            }

            $systemM->link->commit();
        } catch (\Exception $ex) {
            $systemM->link->rollback();
            return json($info);
        }

        $info['status'] = 1;
        $info['message'] = "消息发送成功";
        return json($info);
    }

    //获取消息列表
    public function get_msg_list(){
        $info = ['status'=>0,'message'=>"获取列表失败"];

        $uid = input('uid',0,'int');
        $type = input('type',0,'int');
        $status = input('status','','string');

        if (!$uid) {
            $info['message'] = "用户id不能为空";
            return json($info);
        }

        $systemM = new SystemMessageModel($this->corp_id);
        $msg_list = $systemM->getMsgList($uid,$type,$status);

        $info['status'] = 1;
        $info['message'] = "获取成功";
        $info['data'] = $msg_list;
        return json($info);
    }
    //获取某条消息
    public function get_msg(){
        $info = ['status'=>0,'message'=>"获取消息失败"];

        $uid = input('uid',0,'int');
        $msg_id = input('msg_id',0,'int');
        if (!$uid || !$msg_id) {
            $info['message'] = "请输入正确的消息或员工id";
            return json($info);
        }

        $systemM = new SystemMessageModel($this->corp_id);
        $msg = $systemM->getOneMsg($uid,$msg_id);

        $info['status'] = 1;
        $info['message'] = "获取成功";
        $info['data'] = $msg;
        return json($info);

    }
    /**
     * 发送消息
     * @param string $from   发送人
     * @param arr $target 接收人数组
     * @param int $msg_id 消息id
     * @param string $msg    消息
     * @param string $url    链接
     * @param int $type   消息类型
     * @param int $to_app 是否发送app
     * @param int $to_pc  是否发送pc
     */
    public function add_msg($from,$target,$msg_id,$msg,$url,$type,$to_app,$to_pc){
        $huanxin = new HuanxinApi();
        $huanxin_flg = $huanxin->sendMessage(
            "users",
            $target,
            $msg,
            "txt",
            $from,
            [
                "message_id"=>$msg_id,
                "message_is_read"=>0,
                "message_type"=>$type,
                "url"=>$url,
                "to_pc"=>$to_pc,
                "to_app"=>$to_app,
            ]
        );

        return $huanxin_flg;
    }
    //删除消息
    public function del_msg(){
        $info = ['status'=>0,'message'=>"删除消息失败"];

        $uid = input('uid',0,'int');
        $msg_ids = ('msg_id/a');
        if (!$uid || empty($msg_ids)) {
            $info['message'] = "请输入正确的消息或员工id";
            return json($info);
        }

        $systemM = new SystemMessageModel($this->corp_id);
        $flg = $systemM->delMsg($uid,$msg_ids);
        if ($flg) {
            $info['status'] = 1;
            $info['message'] = "删除成功";
        }
        
        return json($info);
    }
    public function push_msg(){

    }

    /**
     * 修改已读信息
     * @param [type] $msg_id      [description]
     * @param [type] $receive_uid [description]
     */
    public function set_read_msg($msg_id,$receive_uid){
        $info = ['status'=>0,'message'=>"消息已读失败"];

        if (!$msg_id || !$receive_uid) {
            $info['message'] = "消息id或发送人id为空";
            return json($info);
        }

        $data['status'] = 1;
        $data['create_time'] = time();
        $map['msg_id'] = $msg_id;
        $map['receive_uid'] = $receive_uid;
        
        $systemM = new SystemMessageModel($this->corp_id);
        $systemM->link->startTrans();
        try {
            $flg = $systemM->updateMsgStd($map,$data);
            if (!$flg) {
                $info['error'] = "跟新已读状态数据表失败";
                exception("跟新已读状态数据表失败");
            }

            if ($this->device_type == 1) {
                $target[] = $this->corp_id."_".$receive_uid;
                $flg = $this->send_read_msg($msg_id,$target);
                if ($flg['status'] == 0) {
                    $info['error'] = "发送消息失败";
                    exception("发送消息失败");
                }
            }

            $systemM->link->commit();
        } catch (\Exception $ex) {
            $systemM->lin->rollback();
            return json($info);
        }

        $info['status'] = 1;
        $info['message'] = "已读消息发送成功";

        return json($info);
    }

    /**
     * 发送已读透传消息
     * @param  [type] $msg_id 消息id
     * @param  [type] $target 接收人
     * @return [type]         [description]
     */
    public function send_read_msg($msg_id,$target){
        $huanxin = new HuanxinApi();
        $huanxin_flg = $huanxin->sendMessage(
            "users",
            $target,
            "消息已读",
            "cmd",
            "系统消息",
            [
                "message_id"=>$msg_id,
                "message_is_read"=>1
            ]
        );

        return $huanxin_flg;
    }
    public function set_read_msg_by_type(){

    }
    public function set_read_msg_all(){

    }
}
