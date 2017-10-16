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
use app\common\controller\Initialize;

class RingUp extends Initialize{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 查询通话记录
     * @return string
     */
    public function call_record(){
        $result = ['status'=>0 ,'info'=>"查询通话记录时发生错误！"];
        $customer_id = input('contactor_id',0,'int');
        $contactor_num = input('contactor_num',0,'int');
        $num = 10;
        $p = input("p");
        $p = $p?:1;
        try{
            $CallRecordModel = new CallRecord($this->corp_id);
            $map["userid"] = $this->uid;
            if($customer_id){
                $map['cr.contactor_id'] = $customer_id;
            }
            if($contactor_num){
                $map['cr.main_phone'] = $contactor_num;
            }
            $callRecord = $CallRecordModel->getCallRecord($num,$p,$map);
            $result['data'] = $callRecord;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询通话记录成功！";
        return json($result);
    }
}