<?php
namespace app\verification\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\ContractSetting as ContractModel;
use app\crm\model\Contract as ContractAppliedModel;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\common\model\Employee as EmployeeModel;
use app\common\model\Structure as StructureModel;
use app\verification\model\VerificatioLog;

class Contract extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }
    public function index(){
        $num = input('num',$this->paginate_list_rows,'int');
        $p = input("p",1,"int");
        $customers_count=0;
        $start_num = ($p-1)*$num;
        $end_num = $start_num+$num;
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter(["in_column","order_status","structure","contract_type","business_id","contract_no","apply_employee","customer_name"]);
        $field = $this->_getCustomerField([]);
        try{
            $contractAppliedModel = new ContractAppliedModel($this->corp_id);
            $contractApplieds = $contractAppliedModel->getVerificationContractApplied($uid,$num,$p,$filter,$field,$order,$direction);
//            var_exp($contractApplieds,'$contractApplieds',1);
            $this->assign('list_data',$contractApplieds);
            $customers_count = $contractAppliedModel->getVerificationContractAppliedCount($uid,$filter);
            $this->assign("count",$customers_count);
            $listCount = $contractAppliedModel->getVerificationColumnNum($uid,$filter);
            $this->assign("listCount",$listCount);
            $struM = new StructureModel($this->corp_id);
            $structs = $struM->getAllStructure();
            $this->assign("structs",$structs);
            $contractSettingModel = new ContractModel($this->corp_id);
            $contracts = $contractSettingModel->getAllContract();
            //var_exp($contracts,'$contracts',1);
            $this->assign('contract_type_list',$contracts);
            $businessFlowModel = new BusinessFlowModel($this->corp_id);
            $business_flows = $businessFlowModel->getAllBusinessFlow();
            //var_exp($business_flows,'$business_flows',1);
            $this->assign('business_flows',$business_flows);
            $apply_status_list = getApplyStatusList();
            array_pop($apply_status_list);
            //var_exp($status_list,'$status_list',1);
            $this->assign('apply_status_list',$apply_status_list);
        }catch (\Exception $ex){
            print_r($ex->getTrace());die();
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
        return view();
    }
    protected function _getCustomerFilter($filter_column){
        $filter = [];

        //订单状态
        if(in_array("order_status", $filter_column)){
            $in_column = input("order_status",-1,"int");
            if($in_column>=0){
                $filter["order_status"] = $in_column;
            }
        }

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
    public function approved(){
        $result = ['status'=>0 ,'info'=>"通过合同申请时发生错误！"];
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $remark = input("remark",null,"string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $contractApplied = $contractAppliedM->getContract($id);
        if(empty($contractApplied)){
            $result['info'] = "未找到合同申请！";
            return json($result);
        }
        $contract_apply_status = $contractApplied["contract_apply_status"];
        if(empty($contract_apply_status) || $contract_apply_status>=6){
            $result['info'] = "审批流程出现问题,请联系管理员！";
            return json($result);
        }
        try{
            $contractAppliedM->link->startTrans();

            //审核记录通用数据
            $verificatioLogData["type"] = 1;
            $verificatioLogData["target_id"] = $id;
            $verificatioLogData["create_user"] = $uid;
            $verificatioLogData["create_time"] = $time;
            $verificatioLogRemark = '';

            $applied_data=[];
            if($remark){
                $applied_data["remark"] = ["exp","concat(remark,'".$remark.";')"];
            }
            $map["status"] = 0;


            //生成合同
            $contractSettingModel = new ContractModel($this->corp_id);
            $contract_setting = $contractSettingModel->getContractSettingById($contractApplied["contract_type"]);
            //如果当前审核生成合同号
            if($contract_setting["create_contract_num_".$contract_apply_status]==1){
                $contract_num = $contractApplied["contract_num"];
                $now_contract_no = $contract_setting["current_contract"];
                $end_contract_no = $now_contract_no+$contract_num-1;
                $contract_prefix = $contract_setting["contract_prefix"];
                if($end_contract_no>$contract_setting["end_num"]){
                    exception("审批失败,剩余合同号数量不足！");
                }
                $contract_arr = [];
                $contract_item["applied_id"] = $id;
                $contract_item["update_time"] = $time;
                $contract_item["create_time"] = $time;
                $contract_item["status"] = 4;
                for($contract_no=$now_contract_no;$contract_no<=$end_contract_no;$contract_no++){
                    $contract_item["contract_no"] = $contract_prefix.$contract_no;
                    $contract_arr[] = $contract_item;
                }
                $contractCreateFlg = $contractAppliedM->createContractNos($contract_arr);
                if(!$contractCreateFlg){
                    exception("审批失败,生成合同号时出现错误！");
                }
                $contractSettingModel = new ContractModel($this->corp_id);
                $contract_setting_flg = $contractSettingModel->setContractSetting(
                    $contractApplied["contract_type"],
                    ["current_contract"=>["exp","current_contract + ".$contract_num]]//$contract_num
                );
                if(!$contract_setting_flg){
                    exception("审批失败,更新合同当前合同号时出现错误！");
                }
                $verificatioLogRemark .= "生成合同号!";
            }

            if(
                $contract_apply_status!=6 &&
                !empty($contractApplied["contract_apply_".($contract_apply_status+1)])
            ){
                //还有下一步审批,转为下一个人审批
                $applied_data["contract_apply_now"] = $contractApplied["contract_apply_".($contract_apply_status+1)];
                $applied_data["contract_apply_status"] = $contract_apply_status+1;
                $contractAppliedFlg = $contractAppliedM->setContract($id,$applied_data,$map);
                if(!$contractAppliedFlg){
                    $result['info'] = "审批失败！";
                    return json($result);
                }
                
                $verificatioLogRemark .= "审核通过,转到下一审核人";
                $verificatioLogData["status_previous"] = $contract_apply_status;
                $verificatioLogData["status_now"] = $contract_apply_status+1;
            }else{
                //最后一步审批,审批通过
                $applied_data["status"] = 1;
                $contractAppliedFlg = $contractAppliedM->setContract($id,$applied_data,$map);
                if(!$contractAppliedFlg){
                    exception("审批失败！");
                }

                $contractCreateFlg = $contractAppliedM->updateContractNos($id);
                if(!$contractCreateFlg){
                    exception("审批失败,更新合同信息时出现错误！");
                }

                $verificatioLogRemark .= "审核最终通过!";
                $verificatioLogData["status_previous"] = $contractApplied["status"];
                $verificatioLogData["status_now"] = $applied_data["status"];
            }

            //保存审核记录
            $verificatioLogData["remark"] = $verificatioLogRemark;
            $verificatioLogM = new VerificatioLog($this->corp_id);
            $verificatioLogAddFlg = $verificatioLogM->addVerificatioLog($verificatioLogData);
            if(!$verificatioLogAddFlg){
                exception("审批失败,保存审批记录时出现错误！");
            }
            $contractAppliedM->link->commit();
        }catch (\Exception $ex){
            $contractAppliedM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status']=1;
        $result['info']='通过合同申请成功!';
        return $result;
    }
    public function rejected(){
        $result = ['status'=>0 ,'info'=>"驳回合同申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        try{
            $contractAppliedM->link->startTrans();
            $update_flg = $contractAppliedM->rejected($id);
            if(!$update_flg){
                $result['info'] = "驳回合同失败！";
                return json($result);
            }
            $verificatioLogData["type"] = 1;
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
            $contractAppliedM->link->commit();
        }catch (\Exception $ex){
            $contractAppliedM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status']=1;
        $result['info']='驳回合同申请成功!';
        return $result;
    }
    public function invalid(){
        $result = ['status'=>0 ,'info'=>"作废合同时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='作废合同开发中!';
        return $result;
    }
    public function received(){
        $result = ['status'=>0 ,'info'=>"已领取合同时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        try{
            $contractAppliedM->link->startTrans();
            $update_flg = $contractAppliedM->received($id);
            if(!$update_flg){
                $result['info'] = "已领取合同失败！";
                return json($result);
            }
            $verificatioLogData["type"] = 1;
            $verificatioLogData["target_id"] = $id;
            $verificatioLogData["create_user"] = $uid;
            $verificatioLogData["create_time"] = $time;
            $verificatioLogData["status_previous"] = 0;
            $verificatioLogData["status_now"] = 2;
            $verificatioLogData["remark"] = "已领取合同";
            $verificatioLogM = new VerificatioLog($this->corp_id);
            $verificatioLogAddFlg = $verificatioLogM->addVerificatioLog($verificatioLogData);
            if(!$verificatioLogAddFlg){
                exception("审批失败,保存审批记录时出现错误！");
            }
            $contractAppliedM->link->commit();
        }catch (\Exception $ex){
            $contractAppliedM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status']=1;
        $result['info']='已领取合同成功!';
        return $result;
    }
    public function remind(){
        $result = ['status'=>0 ,'info'=>"提醒领取合同时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='提醒领取合同开发中!';
        return $result;
    }
    public function refunded(){
        $result = ['status'=>0 ,'info'=>"已退款时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='已退款开发中!';
        return $result;
    }
    public function withdrawal(){
        $result = ['status'=>0 ,'info'=>"收回合同时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $contractAppliedM = new ContractAppliedModel($this->corp_id);
        try{
            $contractAppliedM->link->startTrans();
            $update_flg = $contractAppliedM->withdrawal($id);
            if(!$update_flg){
                $result['info'] = "收回合同失败！";
                return json($result);
            }
            $verificatioLogData["type"] = 1;
            $verificatioLogData["target_id"] = $id;
            $verificatioLogData["create_user"] = $uid;
            $verificatioLogData["create_time"] = $time;
            $verificatioLogData["status_previous"] = 0;
            $verificatioLogData["status_now"] = 2;
            $verificatioLogData["remark"] = "收回合同";
            $verificatioLogM = new VerificatioLog($this->corp_id);
            $verificatioLogAddFlg = $verificatioLogM->addVerificatioLog($verificatioLogData);
            if(!$verificatioLogAddFlg){
                exception("审批失败,保存审批记录时出现错误！");
            }
            $contractAppliedM->link->commit();
        }catch (\Exception $ex){
            $contractAppliedM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status']=1;
        $result['info']='收回合同成功!';
        return $result;
    }
}