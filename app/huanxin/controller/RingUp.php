<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\huanxin\controller;

use app\crm\model\CallRecord;

class RingUp{

    /**
     * 查询通话记录
     * @param \app\huanxin\controller\User $user
     * @return string
     */
    public function call_record(User $user){
        $userid = input('param.userid');
        $access_token = input('param.access_token');
        $chk_info = $user->checkUserAccess($userid,$access_token);
        if (!$chk_info['status']) {
            return json($chk_info);
        }
        $result = ['status'=>0 ,'info'=>"查询客户列信息时发生错误！"];
        $customer_id = input('customer_id',0,'int');
        $num = 10;
        $p = input("p");
        $p = $p?:1;
        try{
            $CallRecordModel = new CallRecord($chk_info['corp_id']);
            $map["userid"] = $chk_info["userinfo"]["id"];
            if($customer_id){
                $map['customer_id'] = $customer_id;
            }
            $callRecord = $CallRecordModel->getCallRecord($num,$p,$map);
            $result['data'] = $callRecord;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询客户列信息成功！";
        return json($result);
    }
}