<?php
/**
 * Created by messhair
 * Date: 17-3-16
 */
namespace app\huanxin\service;

use app\huanxin\model\RedEnvelope;
use app\common\model\Employer;
use app\huanxin\model\TakeCash;

class OverTimeRedEnvelope
{
    protected $corp_id;
    protected $red_ids;
    protected $redM;
    protected $userid;

    /**
     * 超时红包检测对象
     * @param $userid 用户id非tel
     * @param $corp_id 公司代号
     * @param string $red_data 领取红包时的一条红包数据
     */
    public function __construct($userid,$corp_id,$red_data='')
    {
        $this->corp_id = $corp_id;
        $this->userid = $userid;
        $this->redM = new RedEnvelope($this->corp_id);

        //获取超时红包数据array
        if (!empty($red_data)) {
            $this->red_ids = $this->redM->getOverTimeRedIdsFromRedId($red_data['redid']);
        } else {
            $this->red_ids = $this->redM->getOverTimeRedIdsFromUserId($userid);
        }
    }

    /**
     * 红包返还到账号
     * @return bool
     */
    public function sendBackOverTimeRed()
    {
        if (!empty($this->red_ids)) {
            $arr = [
                'ids'=>[],
                'money'=>0,
                'fromuser'=>''
            ];
            foreach ($this->red_ids as $val) {
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
                'userid'=> $this->userid,
                'take_money'=>$money,
                'status'=>2,
                'truename'=>$chk_info['userinfo']['truename'],
                'took_time'=>$time,
                'remark' => '红包到期返还'
            ];
            $cashM = new TakeCash($this->corp_id);
            $employM = new Employer($this->corp_id);
            $this->redM->link->startTrans();
            try{
                $change_took_state = $this->redM->setOverTimeRed($arr['ids'],$time);
                $send_back_money = $employM->setSingleEmployerInfobyId($arr['fromuser'],$employer_data);
                $cash_rec = $cashM->addOrderNumber($cash_data);
            }catch(\Exception $e){
                $this->redM->link->rollback();
            }
            if ($change_took_state > 0 && $send_back_money >0 && $cash_rec > 0) {
                $this->redM->link->commit();
                write_log($arr['fromuser'],2,'收到返还的超时红包，id为'.$arr['ids'].'返还金额'.$money.'分',$this->corp_id);
                return true;
            } else {
                $this->redM->link->rollback();
                write_log($arr['fromuser'],2,'返还超时红包失败，id为'.$arr['ids'].'返还金额'.$money.'分',$this->corp_id);
                return false;
            }
        }
        return true;
    }
}