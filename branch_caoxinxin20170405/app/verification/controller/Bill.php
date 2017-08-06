<?php
namespace app\verification\controller;

use app\common\controller\Initialize;
use app\crm\model\Bill as BillModel;
use app\common\model\Employee as EmployeeModel;
use app\systemsetting\model\BillSetting as BillSettingModel;
use app\verification\model\VerificatioLog;

class Bill extends Initialize{
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
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter([]);
        $field = $this->_getCustomerField([]);
        //$filter["employee_id"] = $uid; // 审核人
        $filter["status"] = 1;
        try{
            $billM = new BillModel($this->corp_id);
            $bill_list = $billM->getBill($num,$p,$filter,$field,$order,$direction);
            //var_exp($bill_list,'$bill_list',1);
            $employee_ids = [];
            foreach ($bill_list as &$bill){
                $handle_status = $bill["handle_status"];
                if(
                    isset($bill["handle_".$handle_status]) &&
                    !empty($bill["handle_".$handle_status])
                ){
                    $employee_ids[] = $bill["handle_".$handle_status];
                    $bill["assessor"] = $bill["handle_".$handle_status];
                }
            }
            $employeeM = new EmployeeModel($this->corp_id);
            $employee_name_index = $employeeM->getEmployeeNameByUserids($employee_ids);
            foreach ($bill_list as &$bill){
                if(
                    isset($bill["assessor"])&&
                    isset($employee_name_index[$bill["assessor"]])
                ) {
                    $bill["assessor_name"] = $employee_name_index[$bill["assessor"]];
                }else{
                    $bill["assessor_name"] = '';
                }
            }
            $this->assign('list_data',$bill_list);
            $customers_count = $billM->getBillCount($filter);
            $this->assign("count",$customers_count);
            $billSettingModel = new BillSettingModel($this->corp_id);
            $bills = $billSettingModel->getBillNameIndex();
            //var_exp($bills,'$bills',1);
            $this->assign('bill_name',$bills);
        }catch (\Exception $ex){
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
        return $filter;
    }
    protected function _getCustomerField($field_column){
        $field = [];
        return $field;
    }
    public function approved(){
        $result = ['status'=>0 ,'info'=>"通过发票申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $remark = input("remark",null,"string");
        $bill_no = input("bill_no",null,"string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

        $billM = new BillModel($this->corp_id);
        $bill_info = $billM->getBillById($id);
        $bill_handle_status = $bill_info["handle_status"];

        try{
            $billM->link->startTrans();

            //审核记录通用数据
            $verificatioLogData["type"] = 3;
            $verificatioLogData["target_id"] = $id;
            $verificatioLogData["create_user"] = $uid;
            $verificatioLogData["create_time"] = $time;
            $verificatioLogRemark = '';

            $bill_data = [];
            if($remark){
                $bill_data["remark"] = ["exp","concat(remark,'".$remark.";')"];
            }
            if($bill_no){
                $bill_data["bill_no"] = $bill_no;
                $verificatioLogRemark .= "填写发票号!";
            }
            $map["status"] = 0;

            if(
                $bill_handle_status!=6 &&
                !empty($bill_info["handle_".($bill_handle_status+1)])
            ){
                //还有下一步审批,转为下一个人审批
                $bill_data["handle_status"] = $bill_handle_status+1;
                $update_flg = $billM->setBill($id,$bill_data,$map);
                if(!$update_flg){
                    $result['info'] = "审批失败！";
                    return json($result);
                }

                $verificatioLogRemark .= "审核通过,转到下一审核人";
                $verificatioLogData["status_previous"] = $bill_handle_status;
                $verificatioLogData["status_now"] = $bill_handle_status+1;
            }else{
                //最后一步审批
                //$bill_data["status"] = 1;
                $bill_data["status"] = 4;
                $update_flg = $billM->setBill($id,$bill_data,$map);
                if(!$update_flg){
                    $result['info'] = "通过发票申请失败！";
                    return json($result);
                }

                $verificatioLogRemark .= "审核最终通过!";
                $verificatioLogData["status_previous"] = $bill_info["status"];
                $verificatioLogData["status_now"] = $bill_data["status"];
            }

            //保存审核记录
            $verificatioLogData["remark"] = $verificatioLogRemark;
            $verificatioLogM = new VerificatioLog($this->corp_id);
            $verificatioLogAddFlg = $verificatioLogM->addVerificatioLog($verificatioLogData);
            if(!$verificatioLogAddFlg){
                exception("审批失败,保存审批记录时出现错误！");
            }
            $billM->link->commit();
        }catch (\Exception $ex){
            $billM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }

        $result['status']=1;
        $result['info']='通过发票申请成功!';
        return $result;
    }
    public function rejected(){
        $result = ['status'=>0 ,'info'=>"驳回发票申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $billM = new BillModel($this->corp_id);
        try{
            $billM->link->startTrans();
            $update_flg = $billM->rejected($id);
            if(!$update_flg){
                $result['info'] = "驳回发票申请失败！";
                return json($result);
            }
            $verificatioLogData["type"] = 3;
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
            $billM->link->commit();
        }catch (\Exception $ex){
            $billM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status']=1;
        $result['info']='驳回发票申请成功!';
    }
    public function received(){
        $result = ['status'=>0 ,'info'=>"已领取发票时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $billM = new BillModel($this->corp_id);
        $update_flg = $billM->received($id);
        if(!$update_flg){
            $result['info'] = "已领取发票失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='已领取发票成功!';
        return $result;
    }
    public function invalid(){
        $result = ['status'=>0 ,'info'=>"作废发票时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $billM = new BillModel($this->corp_id);
        $update_flg = $billM->invalid($id);
        if(!$update_flg){
            $result['info'] = "作废发票失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='作废发票成功!';
        return $result;
    }
}