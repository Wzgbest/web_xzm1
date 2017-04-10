<?php
/**
 * Created by messhair.
 * Date: 2017/4/10
 * php think queue:listen
 */
namespace app\huanxin\job;

use think\queue\Job;
use app\common\model\Employer;
use app\huanxin\model\RedEnvelope;
use app\huanxin\model\TakeCash;

class RecordRedEnvelope
{
    public function fire(Job $job,$data)
    {
        $data = json_decode($data,true);
        $corp_id = $data['r']['corp_id'];
        $red_data = $data['red_data'];
        $records = $data['records'];
        $add = $data['add'];
        $cash_data = $data['cash_data'];
        $redM = new RedEnvelope($corp_id);
        $employerM = new Employer($corp_id);
        $cashM = new TakeCash($corp_id);
        $redM->link->startTrans();
        try{
            $res = $redM->setOneRedId($red_data['id'],$records);
            $re = $employerM->setEmployerSingleInfo($data['r']['userinfo']['id'],$add);
            $cash_rec = $cashM->addOrderNumber($cash_data);
        }catch(\Exception $e){
            $redM->link->rollback();
        }
        if ($res > 0 && $re > 0 && $cash_rec > 0) {
            $redM->link->commit();
            write_log($data['r']['userinfo']['id'],2,'用户领取红包,金额'.$red_data['money'].'元',$corp_id);
        } else {
            $redM->link->rollback();
//            $info['message'] = '红包已被抢光了';
//            $info['errnum'] = 5;
        }
    }
}