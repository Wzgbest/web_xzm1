<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\crm\model\CallRecord;

class RingUp extends Initialize{
    public function index(){
        echo "crm/ring_up/index";
    }

    public function call_record(){
        $result = ['status'=>0 ,'info'=>"查询客户列信息时发生错误！"];
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $customer_id = input('customer_id',0,'int');
        $num = 10;
        $p = input("p");
        $p = $p?:1;
        try{
            $CallRecordModel = new CallRecord($this->corp_id);
            $map["userid"] = $uid;
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