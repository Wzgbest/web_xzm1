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


class EmployeeTask{
    protected $corp_id;
    public function __construct($corp_id=null){
        $this->corp_id = $corp_id;
    }
    public function getRankingList($target_type,$task_method,$start_time,$end_time,$uids,$task_id,$standard=0,$num=20,$page=1){
        $data = [];
        switch ($target_type){
            case 1:
                $callRecordM = new CallRecord($this->corp_id);
                if($task_method==1){
                    $data = $callRecordM->getCallRecordStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }elseif($task_method==5){
                    $data = [];
                }else{
                    $data = $callRecordM->getCallRecordRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 2:
                $saleChanceM = new SaleChance($this->corp_id);
                if($task_method==1){
                    $data = $saleChanceM->getSaleChanceStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }elseif($task_method==5){
                    $data = [];
                }else{
                    $data = $saleChanceM->getSaleChanceRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 3:
                $saleOrderContractM = new SaleOrderContract($this->corp_id);
                if($task_method==1){
                    $data = $saleOrderContractM->getOrderMoneyStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }elseif($task_method==5){
                    $data = [];
                }else{
                    $data = $saleOrderContractM->getOrderMoneyRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 4:
                $saleOrderContractM = new SaleOrderContract($this->corp_id);
                if($task_method==1){
                    $data = $saleOrderContractM->getSaleOrderContractStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }elseif($task_method==5){
                    $data = [];
                }else{
                    $data = $saleOrderContractM->getSaleOrderContractRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 5:
                $saleChanceVisitM = new SaleChanceVisit($this->corp_id);
                if($task_method==1){
                    $data = $saleChanceVisitM->getSaleChanceVisitStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }elseif($task_method==5){
                    $data = [];
                }else{
                    $data = $saleChanceVisitM->getSaleChanceVisitRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 6:
                $customerM = new Customer($this->corp_id);
                if($task_method==1){
                    $data = $customerM->getCustomerStandard($start_time,$end_time,$uids,$standard,$num,$page);
                }elseif($task_method==5){
                    $data = [];
                }else{
                    $data = $customerM->getCustomerRanking($start_time,$end_time,$uids,$standard,$num,$page);
                }
                break;
            case 7:
                $customerM = new Customer($this->corp_id);
                if($task_method==5){
                    $data = $customerM->getEmployeeRanking($start_time,$end_time,$uids,$task_id,$standard,$num,$page);
                }

        }

        return $data;
    }
}