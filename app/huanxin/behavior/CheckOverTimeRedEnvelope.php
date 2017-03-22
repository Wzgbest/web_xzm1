<?php
/**
 * Created by messhair
 * Date: 17-3-22
 */
namespace app\huanxin\behavior;

use app\huanxin\model\RedEnvelope;
use app\common\model\Employer;
use app\huanxin\model\TakeCash;

class CheckOverTimeRedEnvelope
{
    /**
     * 红包返还到账号
     * @return bool
     */
    public function run($params)
    {
        $par = json_decode($params,true);
        $corp_id = $par['corp_id'];
        $userid = $par['userid'];
        $redM = new RedEnvelope($corp_id);

        //获取超时红包数据array
        if (!empty($par['red_data'])) {
            $red_ids = $redM->getOverTimeRedIdsFromRedId($par['red_data']['redid']);
        } else {
            $red_ids = $redM->getOverTimeRedIdsFromUserId($par['userid']);
        }
        if (!empty($red_ids)) {
            $arr = [
                'ids'=>[],
                'money'=>0,
                'fromuser'=>''
            ];
            foreach ($red_ids as $val) {
                $arr['ids'][] .= $val['id'];
                $arr['money'] +=$val['money'];
                $arr['fromuser'] = $val['fromuser'];
            }
            $arr['ids']= implode(',',$arr['ids']);

            $money = intval($arr['money']* 100);//单位，分
            //用户余额增加
            $employer_data = ['left_money'=>['exp', "left_money + $money"]];
            $time = time();
            $cash_data = [
                'userid'=> $userid,
                'take_money'=>$money,
                'status'=>2,
                'took_time'=>$time,
                'remark' => '红包到期返还'
            ];
            $cashM = new TakeCash($corp_id);
            $employM = new Employer($corp_id);
            $redM->link->startTrans();
            try{
                $change_took_state = $redM->setOverTimeRed($arr['ids'],$time);
                $send_back_money = $employM->setSingleEmployerInfobyId($arr['fromuser'],$employer_data);
                $cash_rec = $cashM->addOrderNumber($cash_data);
            }catch(\Exception $e){
                $redM->link->rollback();
            }
            if ($change_took_state > 0 && $send_back_money >0 && $cash_rec > 0) {
                $redM->link->commit();
                write_log($arr['fromuser'],2,'收到返还的超时红包，id为'.$arr['ids'].'返还金额'.$money.'分',$corp_id);
                dump('send_back');
                return true;
            } else {
                $redM->link->rollback();
                write_log($arr['fromuser'],2,'返还超时红包失败，id为'.$arr['ids'].'返还金额'.$money.'分',$corp_id);
                return false;
            }
        }
        return true;
    }
}