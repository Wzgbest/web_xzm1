<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

namespace app\huanxin\service;

use app\huanxin\model\RedEnvelope as RedB;
use app\common\model\Employee;
use app\huanxin\model\TakeCash;

class RedEnvelope{
    protected $corp_id;
    public function __construct($corp_id=null){
        $this->corp_id = $corp_id;
    }
    public function getRedEnvelope($red_id){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $userid = $userinfo["telephone"];
        $time = time();
        $info['status'] = 0;
        $info['info'] = '红包领取失败';
        $redM = new RedB($this->corp_id);
        $myCount = $redM->getUserRedCount($uid,$red_id);
        if($myCount>0){
            $info['info'] = '您已领取红包';
            $info['status'] = 2;
            return $info;
        }


        $redM->link->startTrans();
        try{
            $getCount = $redM->fetchedRedEnvelope($red_id,$uid,$userid,$time);
            if(!$getCount>0){
                $redM->link->rollback();
                $info['info'] = '红包已被抢光了';
                $info['status'] = 3;
                exception("红包已被抢光了!");
            }
            $red_arr = $redM->getRedInfoByRedId($red_id);
            $already_arr=[];
            $red_data = [];
            foreach ($red_arr as $key => $val) {
                if ($val['took_user'] == $uid) {
                    $red_data = $val;
                }
                if ($val['is_token'] ==1 ) {
                    $already_arr[] = $val;
                }
            }

            $cashM = new TakeCash($this->corp_id);
            $time = time();
            $get_money = $red_data['money']*100;
            //take_cash表记录
            $order_data = [
                'userid'=>$uid,
                'take_type'=>4,
                'take_id'=>$red_id,
                'take_money'=> $get_money,
                'take_status'=>2,
                'took_time'=>$time,
                'remark' => '领取红包',
                "status"=>1
            ];
            $employM = new Employee($this->corp_id);
            $de = $employM->setEmployeeSingleInfo($userid,['left_money'=>['exp',"left_money + ".$get_money]]);
            if(!$de){
                $info['info'] = '更新余额发生错误';
                exception("更新余额发生错误!");
            }
            $cash_rec = $cashM->addOrderNumber($order_data);
            if(!$cash_rec){
                $info['info'] = '添加交易记录发生错误';
                exception("添加交易记录发生错误!");
            }
            $redM->link->commit();
            $info['status'] = 1;
            $info['info'] = '恭喜领取成功';
            $info['data']['money'] = $red_data['money'];;
            $info['data']['red_info'] = $already_arr;
        }catch(\Exception $ex){
            $redM->link->rollback();
            //dump($ex->getTrace());exit;
            return $info;
        }

        return $info;
    }
}