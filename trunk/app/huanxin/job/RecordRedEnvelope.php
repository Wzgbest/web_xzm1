<?php
/**
 * Created by messhair.
 * Date: 2017/4/10
 * php think queue:listen能保证数据正常,缓存返回异常
 * php think queue:work --daemon能保证缓存返回正常，数据异常
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
//        file_put_contents('d:/my.txt',$data);
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
            $re = $employerM->setSingleEmployerInfobyId($data['r']['userinfo']['id'],$add);
            $cash_rec = $cashM->addOrderNumber($cash_data);
        }catch(\Exception $e){
            $redM->link->rollback();
        }
        file_put_contents('d:/my.txt',json_encode($red_data,true).'--'.$data['r']['userinfo']['id'].'--'.$res.'--'.$re.'--'.$cash_rec."\r\n",FILE_APPEND);
        if ($res > 0 && $re > 0 && $cash_rec > 0) {
            $redM->link->commit();
            write_log($data['r']['userinfo']['id'],2,'用户领取红包成功,金额'.$red_data['money'].'分',$corp_id);
        } else {
            $redM->link->rollback();
            write_log($data['r']['userinfo']['id'],2,'用户领取红包失败,金额'.$red_data['money'].'分',$corp_id);
        }
        $job->delete();
    }
}