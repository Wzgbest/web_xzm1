<?php
/**
 * Created by messhair.
 * Date: 2017/5/15
 */
namespace app\systemsetting\behavior;

class checkCustomerTransmit
{
    public function run($params)
    {
        $par = json_decode($params,true);
        $corp_id = $par['corp_id'];
        $userid = $par['userid'];
        //TODO 调用crm模块处理客户
        $customerid =1;
        $to_user ='';
        //记录转移客户信息
        write_customer_log($userid,$customerid,'转移客户到上级领导成功'.$to_user);//TODO 成功失败分别转移
        return [
            'status'=>true,
            'message' => '转移客户成功',
        ];
    }
}