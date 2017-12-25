<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\verification\controller;

use app\common\controller\Initialize;
use app\crm\model\SaleOrderContract as SaleOrderContractModel;
use app\systemsetting\model\ContractSetting as ContractModel;
use app\common\model\Structure as StructureModel;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\verification\model\VerificatioLog;
use app\crm\model\SaleChance as SaleChanceModel;
use app\datacount\model\Datacount;

class Index extends Initialize{
    protected $_activityBusinessFlowItem = [1,2,4];
    var $paginate_list_rows = 10;
    public function __construct(){
        parent::__construct();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $customers_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","soc.create_time","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["in_column","contract_type","structure","business_id","pay_type","order_status","contract_no","apply_employee","customer_name"]);
        $field = $this->_getCustomerField([]);
        $filter["status"] = 1;
        try{
            $saleChanceM = new SaleOrderContractModel($this->corp_id);
            $SaleOrderContractsData = $saleChanceM->getVerificationSaleOrderContractByPage($uid,$num,$p,$filter,$field,$order,$direction);
            //var_exp($SaleOrderContractsData,'$SaleOrderContractsData',1);
            $this->assign("list_data",$SaleOrderContractsData);
            $SaleOrderContractIds = $saleChanceM->getVerificationSaleOrderIdsContractByPage($uid,$filter,$order,$direction);
//            var_exp($SaleOrderContractIds,'$SaleOrderContractIds',1);
            $this->assign("list_ids",json_encode($SaleOrderContractIds,true));
            $customers_count = $saleChanceM->getVerificationSaleChanceCount($uid,$filter);
            $this->assign("count",$customers_count);
            $listCount = $saleChanceM->getVerificationColumnNum($uid,$filter);
            $this->assign("listCount",$listCount);
            $contractSettingModel = new ContractModel($this->corp_id);
            $contracts = $contractSettingModel->getAllContract();
            //var_exp($contracts,'$contracts',1);
            $this->assign('contract_type_list',$contracts);
            $struM = new StructureModel($this->corp_id);
            $structs = $struM->getAllStructure();
            $this->assign("structs",$structs);
            $businessFlowModel = new BusinessFlowModel($this->corp_id);
            $business_flows = $businessFlowModel->getAllBusinessFlowByUserId($uid);
            //var_exp($business_flows,'$business_flows',1);
            $this->assign('business_flows',$business_flows);
            $businessFlowModel = new BusinessFlowModel($this->corp_id);
            $business_flow_names = $businessFlowModel->getAllBusinessFlowName();
            //var_exp($business_flow_names,'$business_flow_names',1);
            $this->assign('business_flow_names',$business_flow_names);
            $apply_status_list = getApplyStatusList();
            array_pop($apply_status_list);
            //var_exp($status_list,'$status_list',1);
            $this->assign('apply_status_list',$apply_status_list);
        }catch (\Exception $ex){
            //print_r($ex->getTrace());die();
            $this->error($ex->getMessage());
        }
        $max_page = ceil($customers_count/$num);
        $in_column = isset($filter["in_column"])?$filter["in_column"]:0;
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
        $this->assign("in_column",$in_column);
        $this->assign("start_num",$customers_count?$start_num+1:0);
        $this->assign("end_num",$end_num<$customers_count?$end_num:$customers_count);
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }
    protected function _getCustomerFilter($filter_column){
        $filter = [];

        //合同类型
        if(in_array("contract_type", $filter_column)){
            $in_column = input("contract_type",0,"int");
            if($in_column){
                $filter["contract_type"] = $in_column;
            }
        }

        //对应部门
        if(in_array("structure", $filter_column)){
            $in_column = input("structure",0,"int");
            if($in_column){
                $filter["structure"] = $in_column;
            }
        }

        //对应业务
        if(in_array("business_id", $filter_column)){
            $in_column = input("business_id",0,"int");
            if($in_column){
                $filter["business_id"] = $in_column;
            }
        }

        //打款方式
        if(in_array("pay_type", $filter_column)){
            $in_column = input("pay_type",0,"int");
            if($in_column){
                $filter["pay_type"] = $in_column;
            }
        }

        //订单状态
        if(in_array("order_status", $filter_column)){
            $in_column = input("order_status",-1,"int");
            if($in_column>=0){
                $filter["order_status"] = $in_column;
            }
        }

        //合同号
        if(in_array("contract_no", $filter_column)){
            $in_column = input("contract_no",'',"string");
            if($in_column){
                $filter["contract_no"] = $in_column;
            }
        }

        //负责人
        if(in_array("apply_employee", $filter_column)){
            $in_column = input("apply_employee",'',"string");
            if($in_column){
                $filter["apply_employee"] = $in_column;
            }
        }

        //客户名称
        if(in_array("customer_name", $filter_column)){
            $in_column = input("customer_name",'',"string");
            if($in_column){
                $filter["customer_name"] = $in_column;
            }
        }

        //所在列
        if(in_array("in_column", $filter_column)){
            $in_column = input("in_column",1,"int");
            if($in_column){
                $filter["in_column"] = $in_column;
            }
        }
        return $filter;
    }
    protected function _getCustomerField($field_column){
        $field = [];
        return $field;
    }
    public function detail(){
        $id = input("id",0,"int");
        if(!$id){
            $this->error("参数错误!");
        }

        $saleChanceM = new SaleOrderContractModel($this->corp_id);
        $SaleOrderContract = $saleChanceM->getSaleOrderByContractId($id);
        $struct_names = explode(",",$SaleOrderContract["struct_name"]);
        $SaleOrderContract["struct_name"] = $struct_names[count($struct_names)-1];
        $this->assign("sale_order_contract",$SaleOrderContract);

        $next_id = 0;
        $previous_id = 0;
        $ids = input("ids","","string");
        $ids_arr = json_decode($ids,true);
//        var_exp($ids_arr,'$ids_arr');
        if($ids_arr){
            $now_idx = array_search($id,$ids_arr);
//            var_exp($now_idx,'$now_idx');
            $next_id = 0;
            $previous_id = 0;
            if($now_idx!==false){
                if($now_idx>0){
                    $previous_id = $ids_arr[$now_idx-1];
                }
//                var_exp($previous_id,'$previous_id');
                if($now_idx<(count($ids_arr)-1)){
                    $next_id = $ids_arr[$now_idx+1];
                }
//                var_exp((count($ids_arr)-1),'(count($ids))');
//                var_exp($next_id,'$next_id');
            }
        }
        $this->assign("sale_order_contract_ids",$ids);
        $this->assign("previous_id",$previous_id);
        $this->assign("next_id",$next_id);
        return view();
    }
    public function approved(){
        if(!($this->checkRule('verification/index/index/approved') || $this->checkRule('verification/index/index/approved_remark'))){
            $result=$this->noRole();
            return json($result);
        }
        $result = ['status'=>0 ,'info'=>"通过成单申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $remark = input("remark",null,"string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContract = $saleOrderContractM->getSaleOrderContract($id);
        //var_exp($saleOrderContract,'$saleOrderContract',1);
        $saleOrderContractStatus = $saleOrderContract["handle_status"];

        try{
            $saleOrderContractM->link->startTrans();

            //审核记录通用数据
            $verificatioLogData["type"] = 2;
            $verificatioLogData["target_id"] = $id;
            $verificatioLogData["create_user"] = $uid;
            $verificatioLogData["create_time"] = $time;
            $verificatioLogRemark = '';

            $applied_data = [];
            if($remark){
                $applied_data["remark"] = ["exp","concat(remark,'".$remark.";')"];
            }
            $map = [];
            $next_recieve_uids = [];
            if(
                $saleOrderContractStatus!=6 &&
                !empty($saleOrderContract["handle_".($saleOrderContractStatus+1)])
            ){
                //还有下一步审批,转为下一个人审批
                $applied_data["handle_now"] = $saleOrderContract["handle_".($saleOrderContractStatus+1)];
                $applied_data["handle_status"] = $saleOrderContractStatus+1;
                $map["status"] = 0;
                $saleOrderContractFlg = $saleOrderContractM->setSaleOrderContract($id,$applied_data,$map);
                if(!$saleOrderContractFlg){
                    $result['info'] = "移交审批失败！";
                    return json($result);
                }
                
                $verificatioLogRemark .= "审核通过,转到下一审核人";
                $verificatioLogData["status_previous"] = $saleOrderContractStatus;
                $verificatioLogData["status_now"] = $saleOrderContractStatus+1;
                $next_recieve_uids[] = $saleOrderContract["handle_".($saleOrderContractStatus+1)];
            }else{
                //最后一步审批
                $map["soc.status"] = 0;
                $map['sc.sale_status'] = 4;
                $applied_data["soc.status"] = 1;
                $applied_data['sc.sale_status'] = 5;
                $saleOrderContractFlg = $saleOrderContractM->approvedSaleOrderContract($id,$applied_data,$map);
                if(!$saleOrderContractFlg){
                    $result['info'] = "审批失败！!";
                    return json($result);
                }

                $datacount["uid"] = $this->uid;
                $datacount["time"] = time();
                $datacount["type"] = 3;
                $datacount["link_id"] = $id;
                $datacount["num"] = $saleOrderContract["final_money"];
                $datacountM = new Datacount();
                $data_count_flg  = $datacountM->addDatacount($datacount);
                if(!$data_count_flg){
                    exception('添加成单统计失败!');
                }
                
                $verificatioLogRemark .= "审核最终通过!";
                $verificatioLogData["status_previous"] = $saleOrderContract["status"];
                $verificatioLogData["status_now"] = $applied_data["soc.status"];
            }

            //保存审核记录
            $verificatioLogData["remark"] = $verificatioLogRemark;
            $verificatioLogM = new VerificatioLog($this->corp_id);
            $verificatioLogAddFlg = $verificatioLogM->addVerificatioLog($verificatioLogData);
            if(!$verificatioLogAddFlg){
                exception("审批失败,保存审批记录时出现错误！");
            }
            $saleOrderContractM->link->commit();
        }catch (\Exception $ex){
            $saleOrderContractM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }

        //销售机会创建人发送消息
        $saleM = new SaleChanceModel($this->corp_id);
        $sale_info = $saleM->getSaleChance($saleOrderContract['sale_id']);
        $user_infomation = $userinfo["userinfo"];
        $received_uids[] = $sale_info['employee_id'];
        save_msg("你的".$sale_info['sale_name']."成单申请".$verificatioLogRemark."  [审核人：".$user_infomation["truename"]."]","/crm/sale_chance/index",$received_uids,4,9,$uid,$saleOrderContract['sale_id']);
        if (!empty($next_recieve_uids)) {
            save_msg("有一份".$sale_info['sale_name']."成单申请待你审核！","/verification/index/detail/id/"+$id,$next_recieve_uids,4,10,$sale_info['employee_id']);
        }
        $result['status']=1;
        $result['info']='通过成单申请成功!';
        return $result;
    }
    public function rejected(){
        if(!($this->checkRule('verification/index/index/rejected'))){
            $result=$this->noRole();
            return json($result);
        }
        $result = ['status'=>0 ,'info'=>"驳回成单申请时发生错误！"];
        $id = input("id",0,"int");
        $remark = input("remark",'',"string");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContract = $saleOrderContractM->getSaleOrderContract($id);
        try{
            $saleOrderContractM->link->startTrans();
            $update_flg = $saleOrderContractM->rejectedSaleOrderContract($id);
            if(!$update_flg){
                $result['info'] = "驳回成单申请失败！";
                return json($result);
            }
            $verificatioLogData["type"] = 2;
            $verificatioLogData["target_id"] = $id;
            $verificatioLogData["create_user"] = $uid;
            $verificatioLogData["create_time"] = $time;
            $verificatioLogData["status_previous"] = 0;
            $verificatioLogData["status_now"] = 2;
            $verificatioLogData["remark"] = "审核被驳回";
            $verificatioLogM = new VerificatioLog($this->corp_id);
            $verificatioLogAddFlg = $verificatioLogM->addVerificatioLog($verificatioLogData);
            if(!$verificatioLogAddFlg){
                exception("审批失败,保存审批记录时出现错误！");
            }
            $saleOrderContractM->link->commit();
        }catch (\Exception $ex){
            $saleOrderContractM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }

         //销售机会创建人发送消息
        $saleM = new SaleChanceModel($this->corp_id);
        $sale_info = $saleM->getSaleChance($saleOrderContract['sale_id']);
        $user_infomation = $userinfo["userinfo"];
        $received_uids[] = $sale_info['employee_id'];
        save_msg("你的成单申请由于[".$remark."]原因被驳回，请重提交申请!  [审核人:".$user_infomation["truename"]."]","/crm/sale_chance/index",$received_uids,4,9,$uid,$saleOrderContract['sale_id']);


        $result['status']=1;
        $result['info']='驳回成单申请成功!';
        return $result;
    }
}
