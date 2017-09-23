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
        /*      //方法1:缓存记录红包信息,速度快
                //红包全部
                $red_arr = cache('red_info_all'.$red_id);
                if (empty($red_arr)) {
                    $red_arr = $redM->getRedInfoByRedId($red_id);
                    if (empty($red_arr)) {
                        $info['status'] = false;
                        $info['message'] = '红包已经过期';
                        $info['errnum'] = 4;
                        return json($info);
                    } else {
                        cache('red_info_all'.$red_id,$red_arr);
                    }
                }

                $already_arr=[];
                // 已领取红包，验证，统计
                foreach ($red_arr as $key => $val) {
                    if ($val['took_user'] == $r['userinfo']['id']) {
                        $info['message'] = '您已领取红包';
                        $info['errnum'] = 2;
                        $info['status'] = false;
                    }
                    if ($val['is_token'] ==1 ) {
                        $already_arr[] = $val;
                    }
                }
                if (!$info['status']) {
                    return json($info);
                }

                foreach ($red_arr as $k => $v) {
                    if ($v['is_token'] == 0) {
                        $red_data = $v;
                        $red_arr[$k]['is_token'] = 1;
                        $red_arr[$k]['took_user'] = $r['userinfo']['id'];
                        $red_arr[$k]['took_telephone'] = $r['userinfo']['telephone'];
                        $red_arr[$k]['took_time'] = $time;
                        $already_arr[]=$red_arr[$k];//领取后增加已领取列表
                        break;
                    }
                }
                if (empty($red_data)) {
                    $info['status'] = false;
                    $info['message'] = '红包已被抢光了';
                    $info['errnum'] = 3;
                    return json($info);
                }
                cache('red_info_all'.$red_id,$red_arr);
                if ( $time > ($red_data['create_time'] + config('red_envelope.overtime')) ) {
                    $params = json_encode([
                        'userid'=>$r['userinfo']['id'],
                        'corp_id'=>$r['corp_id'],
                        'red_data'=>$red_data
                    ],true);
                    Hook::listen('check_over_time_red',$params);
                    $info['status'] = false;
                    $info['message'] = '红包已经过期';
                    $info['errnum'] = 4;
                    return json($info);
                }

                $red_money = $red_data['money']*100;
                //余额增加
                $add = ['left_money'=>['exp',"left_money + $red_money"]];
                //红包领取状态改变
                $records = [
                    'took_time'=>$time,
                    'is_token' =>1,
                    'took_user' => $r['userinfo']['id'],
                    'took_telephone'=>$r['userinfo']['telephone']
                ];
                //take_cash记录
                $cash_data = [
                    'userid'=>$r['userinfo']['id'],
                    'take_money'=>$red_money,
                    'status'=>2,
                    'took_time'=>$time,
                    'remark' => '领取红包'
                ];

                //更新数据库
                $queue_data = json_encode(['red_data'=>$red_data,'r'=>$r,'add'=>$add,'records'=>$records,'cash_data'=>$cash_data],true);
                \think\Queue::push('huanxin/RecordRedEnvelope',$queue_data);// TODO 测试关闭
        //        php think queue:listen 模式下数据正常，缓存异常
        //        php think queue:work --daemon 模式下缓存正常，数据异常
                \think\Queue::later(3,'huanxin/RecordRedEnvelope',$queue_data);// TODO 测试关闭
                //更新缓存
                cache('red_info_all'.$red_id,$red_arr);
        //        file_put_contents('e:/desktop/red.txt',json_encode($red_arr,true)."\r\n",FILE_APPEND);
        */


        //方法2:直接记录到数据库
        /*$params = json_encode([
            'userid'=>$r['userinfo']['id'],
            'corp_id'=>$r['corp_id'],
            'red_data'=>''
        ],true);
        */
        //Hook::listen('check_over_time_red',$params);

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
                'take_money'=> $get_money,
                'status'=>1,
                'took_time'=>$time,
                'remark' => '领取红包'
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
            $info['money'] = $red_data['money'];;
            $info['red_info'] = $already_arr;
        }catch(\Exception $ex){
            $redM->link->rollback();
            //dump($ex->getTrace());exit;
            return $info;
        }

        return $info;
    }
}