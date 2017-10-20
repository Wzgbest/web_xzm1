<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\task\service;

use app\crm\model\SaleOrderContract;
use app\crm\model\CallRecord;
use app\crm\model\SaleChance;
use app\crm\model\SaleChanceVisit;
use app\crm\model\Customer;
use app\Task\model\TaskTake;


class EmployeeTask{
    protected $corp_id;
    public function __construct($corp_id=null){
        $this->corp_id = $corp_id;
    }
    public function getRankingList($target_type,$task_method,$start_time,$end_time,$uids,$task_id,$standard=0,$num=20,$page=1){
        $result = [];
        if(empty($uids)){
            return $result;
        }
        switch ($target_type){
            case 1:
                $callRecordM = new CallRecord($this->corp_id);
                if($task_method==1){
                    $result = $callRecordM->getCallRecordStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }else{
                    $result = $callRecordM->getCallRecordRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 2:
                $saleChanceM = new SaleChance($this->corp_id);
                if($task_method==1){
                    $result = $saleChanceM->getSaleChanceStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }else{
                    $result = $saleChanceM->getSaleChanceRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 3:
                $saleOrderContractM = new SaleOrderContract($this->corp_id);
                if($task_method==1){
                    $result = $saleOrderContractM->getOrderMoneyStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }else{
                    $result = $saleOrderContractM->getOrderMoneyRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 4:
                $saleOrderContractM = new SaleOrderContract($this->corp_id);
                if($task_method==1){
                    $result = $saleOrderContractM->getSaleOrderContractStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }else{
                    $result = $saleOrderContractM->getSaleOrderContractRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 5:
                $saleChanceVisitM = new SaleChanceVisit($this->corp_id);
                if($task_method==1){
                    $result = $saleChanceVisitM->getSaleChanceVisitStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }else{
                    $result = $saleChanceVisitM->getSaleChanceVisitRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 6:
                $customerM = new Customer($this->corp_id);
                if($task_method==1){
                    $result = $customerM->getCustomerStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }else{
                    $result = $customerM->getCustomerRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 7:
                $customerM = new TaskTake($this->corp_id);
                if($task_method==5){
                    $result = $customerM->getEmployeeRanking($start_time,$end_time,$uids,$task_id,$standard,$num,$page);
                }

        }

        return $result;
    }
}