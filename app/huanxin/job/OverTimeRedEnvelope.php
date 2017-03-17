<?php
/**
 * Created by messhair
 * Date: 17-3-14
 */
namespace app\huanxin\job;

use app\common\model\Employer;
use app\huanxin\model\RedEnvelope;
use think\queue\Job;

class OverTimeRedEnvelope
{
    /**
     * CREATE TABLE `guguo_jobs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `queue` varchar(255) NOT NULL,
            `payload` longtext NOT NULL,
            `attempts` tinyint(3) unsigned NOT NULL,
            `reserved` tinyint(3) unsigned NOT NULL,
            `reserved_at` int(10) unsigned DEFAULT NULL,
            `available_at` int(10) unsigned NOT NULL,
            `created_at` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id`)
       ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     */
    /**
     *
     * 红包返还任务
     * @param Job $job
     * @param $data
     * @return bool
     */
    public function fire(Job $job,$data)
    {
        $data =json_decode($data,true);
        $red_data = $data['red_data'];
        $corp_id = $data['corp_id'];
        $user_id = $data['userid'];

        $redM = new RedEnvelope($corp_id);
        if (!empty($red_data)) {
            $red_ids = $redM->getOverTimeRedIdsFromRedId($red_data['redid']);
        } else {
            $red_ids = $redM->getOverTimeRedIdsFromUserId($user_id);
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

            $money = intval($arr['money']* 100);
            $employer_data = ['left_money'=>['exp', "left_money +$money"]];
            $employM = new Employer($corp_id);
            $redM->link->startTrans();
            try{
                $change_took_state = $redM->setOverTimeRed($arr['ids']);
                $send_back_money = $employM->setSingleEmployerInfobyId($arr['fromuser'],$employer_data);
                $b = write_log($arr['fromuser'],2,'收到返还的超时红包，id为'.$arr['ids'].'返还金额'.$money.'分',$corp_id);
            }catch(\Exception $e){
                $redM->link->rollback();
            }
            if ($change_took_state > 0 && $send_back_money >0 && $b > 0) {
                $redM->link->commit();
                return true;
            } else {
                $redM->link->rollback();
                return false;
            }
        }
        return true;
    }
}