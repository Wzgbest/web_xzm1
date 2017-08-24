<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Corporation as CorporationModel;
use app\huanxin\model\RedEnvelope;
use app\common\model\Employee;
use app\huanxin\model\TakeCash;

class BackOverTimeRedEnvelope extends Command{
    protected function configure(){
        $this->setName('back_over_time_red_envelope')->setDescription('check over time red envelope and back money to employee');
    }

    protected function execute(Input $input, Output $output){
        $output_info_str = '';
        $time = time();
        $corpIds = CorporationModel::getAllCorpIds();
        $all_red_infos = [];
        $success_red_ids = [];
        $error_red_ids = [];
        foreach ($corpIds as $corpIdArr){
            $corp_id = $corpIdArr["corp_id"];
            $redM = new RedEnvelope($corp_id);
            $red_infos = $redM->getOverTimeRedIds();
            $all_red_infos[$corp_id] = $red_infos;
        }
        foreach ($all_red_infos as $corp_id=>$red_infos){
            $redM = new RedEnvelope($corp_id);
            $cashM = new TakeCash($corp_id);
            $employM = new Employee($corp_id);
            foreach($red_infos as $red_info){
                $red_id = $red_info["id"];
                $from_user = $red_info["fromuser"];
                $money = intval($red_info['money']* 100);//单位，分
                //用户余额增加
                $employee_data = ['left_money'=>['exp', "left_money + $money"]];
                $cash_data = [
                    'userid'=> $from_user,
                    'take_money'=>$money,
                    'status'=>2,
                    'took_time'=>$time,
                    'remark' => '红包到期返还'
                ];
                $redM->link->startTrans();
                try{
                    $change_took_state = $redM->setOverTimeRed($red_id,$time);
                    if(!$change_took_state){
                        exception("");
                    }
                    $send_back_money = $employM->setSingleEmployeeInfobyId($from_user,$employee_data);
                    if(!$send_back_money){
                        exception("");
                    }
                    $cash_rec = $cashM->addOrderNumber($cash_data);
                    if(!$cash_rec){
                        exception("");
                    }
                    $redM->link->commit();
                    $success_red_ids[] = $red_id;
                    write_log($from_user,2,'收到返还的超时红包，id为'.$red_id.'返还金额'.$money.'分',$corp_id);
                }catch(\Exception $e){
                    $redM->link->rollback();
                    $error_red_ids[] = $red_id;
                    write_log($from_user,2,'返还超时红包失败，id为'.$red_id.'返还金额'.$money.'分',$corp_id);
                }
            }
        }


        $output_info_str .= "BOTRE->time:".$time.";success:".count($success_red_ids).";error:".count($error_red_ids).";";

        $trace_info_str = '';
        $trace_info_str .= var_exp($all_red_infos,'$all_red_infos','return',false).";";
        $trace_info_str .= var_exp($success_red_ids,'$success_red_ids','return',false).";";
        $trace_info_str .= var_exp($error_red_ids,'$error_red_ids','return',false).";";
        $trace_info_str .= "\r\n".$output_info_str;
        trace($trace_info_str);

        $output->writeln($output_info_str);
    }
}