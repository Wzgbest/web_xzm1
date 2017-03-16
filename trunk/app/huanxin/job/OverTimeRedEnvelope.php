<?php
/**
 * Created by messhair
 * Date: 17-3-14
 */
namespace app\huanxin\service;

use app\common\model\Employer;
use app\huanxin\model\RedEnvelope;
use think\queue\Job;

class OverTimeRedEnvelope
{
//    public function overTimeJob($corp_id,$now,$red_id)
//    {
////        $corp_ids =Corporation::getAllCorpIds();
//        $time = time();
//        if ($time > $now + 3600*24) {
//            $redM = new RedEnvelope($corp_id);
//            $red_data = $redM->getOverTimeRed($red_id);
//            if (!empty($red_data)) {
//                $arr = [
//                    'ids'=>[],
//                    'money'=>0,
//                    'fromuser'=>''
//                ];
//                foreach ($red_data as $val) {
//                    $arr['ids'][] .= $val['id'];
//                    $arr['money'] +=$val['money'];
//                    $arr['fromuser'] = $val['fromuser'];
//                }
//                $arr['ids']= implode(',',$arr['ids']);
//
//                $money = intval($arr['money']* 100);
//                $employer_data = ['left_money'=>['exp', "left_money +$money"]];
//                $employM = new Employer($corp_id);
//                $redM->link->startTrans();
//                try{
//                    $change_took_state = $redM->setOverTimeRed($arr['ids']);
//                    $send_back_money = $employM->setSingleEmployerInfobyId($arr['fromuser'],$employer_data);
//                    $b = write_log($arr['fromuser'],2,'红包超时返还，id为'.$red_id.'返还金额'.$money.'分',$corp_id);
//                }catch(\Exception $e){
//                    $redM->link->rollback();
//                }
//
//                if ($change_took_state > 0 && $send_back_money >0 && $b > 0) {
//                    $redM->link->commit();
//                } else {
//                    $redM->link->rollback();
//                }
//            }
//        }
//    }

    public function fire(Job $job,$data)
    {
        file_put_contents('/home/joshua/mysvn/webcall/trunk/write/1.txt',$data,FILE_APPEND);
    }
}