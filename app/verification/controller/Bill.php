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
        $filter = $this->_getCustomerFilter(["in_column","bill_type","product_type","pay_type","apply_employee","customer_name","tax_num"]);
        $field = $this->_getCustomerField([]);
        $filter["status"] = 1;
        try{
            $billM = new BillModel($this->corp_id);
            $bill_list = $billM->getVerificationBill($uid,$num,$p,$filter,$field,$order,$direction);
            //var_exp($bill_list,'$bill_list',1);
            foreach ($bill_list as &$bill){
                $handle_status = $bill["handle_status"];
                if(
                    isset($bill["handle_".$handle_status]) &&
                    !empty($bill["handle_".$handle_status])
                ){
                    $bill["now_handle_create_item"] = "create_bill_num_".$handle_status;
                }
            }
            $this->assign('list_data',$bill_list);
            $customers_count = $billM->getVerificationBillCount($uid,$filter);
            $this->assign("count",$customers_count);
            $listCount = $billM->getVerificationColumnNum($uid,$filter);
            $this->assign("listCount",$listCount);
            $billSettingModel = new BillSettingModel($this->corp_id);
            $bills = $billSettingModel->getBillNameIndex();
            //var_exp($bills,'$bills',1);
            $this->assign('bill_name',$bills);
            $payTypeName = $billM->getAllPayTypeName();
            //var_exp($payTypeName,'$payTypeName',1);
            $this->assign('pay_type_name',$payTypeName);
            $productTypeName = $billM->getAllProductTypeName();
            //var_exp($productTypeName,'$productTypeName',1);
            $this->assign('product_type_name',$productTypeName);
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
        $this->assign('rule_white_list',$this->rule_white_list);//权限白名单
        return view();
    }
    public function approved_page(){
        $id = input("id",0,"int");
        if(!$id){
            $this->error("参数错误!");
        }
        $billM = new BillModel($this->corp_id);
        $bill_info = $billM->getBillById($id);
        $bill_handle_status = $bill_info["handle_status"];
        $billSettingModel = new BillSettingModel($this->corp_id);
        $contractSetting = $billSettingModel->getBillSettingById($bill_info["bill_type"]);
        $input_bill_no = 0;
        if($contractSetting["create_bill_num_".$bill_handle_status]==1){
            $input_bill_no = 1;
        }
        $this->assign("input_bill_no",$input_bill_no);
        $bill_last = $billM->getLastBillByType($bill_info["bill_type"]);
        //var_exp($bill_last,'$bill_last',1);
        $this->assign('bill_last',$bill_last);
        return view();
    }
    public function rejected_page(){
        return view();
    }
    protected function _getCustomerFilter($filter_column){
        $filter = [];

        //对应业务
        if(in_array("bill_type", $filter_column)){
            $in_column = input("bill_type",0,"int");
            if($in_column){
                $filter["bill_type"] = $in_column;
            }
        }

        //产品类型
        if(in_array("product_type", $filter_column)){
            $in_column = input("product_type",'',"string");
            if($in_column){
                $filter["product_type"] = $in_column;
            }
        }

        //收款银行
        if(in_array("pay_type", $filter_column)){
            $in_column = input("pay_type",'',"string");
            if($in_column){
                $filter["pay_type"] = $in_column;
            }
        }

        //申请人
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

        //公司税号
        if(in_array("tax_num", $filter_column)){
            $in_column = input("tax_num",'',"string");
            if($in_column){
                $filter["tax_num"] = $in_column;
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
        if(!($this->checkRule('verification/bill/index/approved'))){
            $result=$this->noRole();
            return json($result);
        }
        $result = ['status'=>0 ,'info'=>"通过发票申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $remark = input("remark",null,"string");
        $bill_no = '';
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();

        $billM = new BillModel($this->corp_id);
        $bill_info = $billM->getBillById($id);
        $bill_handle_status = $bill_info["handle_status"];
        $billSettingModel = new BillSettingModel($this->corp_id);
        $contractSetting = $billSettingModel->getBillSettingById($bill_info["bill_type"]);
        if($contractSetting["create_bill_num_".$bill_handle_status]==1){
            $bill_no = input("bill_no",'',"string");
            if(empty($bill_no)){
                $result['info'] = "参数错误！";
                return json($result);
            }
        }

        try{
            $billM->link->startTrans();

            //审核记录通用数据
            $verificatioLogData["type"] = 3;
            $verificatioLogData["target_id"] = $id;
            $verificatioLogData["create_user"] = $uid;
            $verificatioLogData["create_time"] = $time;
            $verificatioLogRemark = '';

            $bill_data = [];
            $next_received_uids = [];
            if($remark){
                $bill_data["remark"] = ["exp","concat(remark,'".$remark.";')"];
            }
            if(!empty($bill_no)){
                $bill_data["bill_no"] = $bill_no;
                $verificatioLogRemark .= "填写发票号!";
            }
            $map["status"] = 0;

            if(
                $bill_handle_status!=6 &&
                !empty($bill_info["handle_".($bill_handle_status+1)])
            ){
                //还有下一步审批,转为下一个人审批
                $bill_data["handle_now"] = $bill_info["handle_".($bill_handle_status+1)];
                $bill_data["handle_status"] = $bill_handle_status+1;
                $update_flg = $billM->setBill($id,$bill_data,$map);
                if(!$update_flg){
                    $result['info'] = "审批失败！";
                    return json($result);
                }

                $verificatioLogRemark .= "审核通过,转到下一审核人";
                $verificatioLogData["status_previous"] = $bill_handle_status;
                $verificatioLogData["status_now"] = $bill_handle_status+1;
                $next_received_uids[] = $bill_info["handle_".($bill_handle_status+1)];
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

        $user_infomation = $userinfo["userinfo"];
        $received_uids[] = $bill_info['operator'];
        $flg = save_msg("你的".$contractSetting['bill_type']."发票".$verificatioLogRemark."  [审核人：".$user_infomation["truename"]."]","/crm/bill/index",$received_uids,4,9,$uid,$bill_info['sale_id']);
        if (!empty($next_received_uids)) {
            save_msg("有一份".$contractSetting['bill_type']."的发票申请待你审核！","/verification/contract/index",$next_received_uids,4,11,$bill_info['operator'],$bill_info['sale_id']);
        }

        $result['status']=1;
        $result['info']='通过发票申请成功!';
        return $result;
    }
    public function rejected(){
        if(!($this->checkRule('verification/bill/index/rejected'))){
            $result=$this->noRole();
            return json($result);
        }
        $result = ['status'=>0 ,'info'=>"驳回发票申请时发生错误！"];
        $id = input("id",0,"int");
        $remark = input("remark","","string");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $billM = new BillModel($this->corp_id);
        $bill_info = $billM->getBillById($id);
        $billSettingModel = new BillSettingModel($this->corp_id);
        $contractSetting = $billSettingModel->getBillSettingById($bill_info["bill_type"]);
        try{
            $billM->link->startTrans();
            $update_flg = $billM->rejected($id);
            if(!$update_flg){
                exception("驳回发票申请失败！");
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

        $user_infomation = $userinfo["userinfo"];
        $received_uids[] = $bill_info['operator'];
        save_msg("你的".$contractSetting['bill_type']."发票审核由于[".$remark."]原因被驳回，请重提交申请!  [审核人:".$user_infomation["truename"]."]","/crm/bill/index",$received_uids,4,9,$uid,$bill_info['sale_id']);

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
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $time = time();
        $billM = new BillModel($this->corp_id);
        try{
            $billM->link->startTrans();
            $update_flg = $billM->received($id);
            if(!$update_flg){
                exception("已领取发票失败！");
            }
            $verificatioLogData["type"] = 3;
            $verificatioLogData["target_id"] = $id;
            $verificatioLogData["create_user"] = $uid;
            $verificatioLogData["create_time"] = $time;
            $verificatioLogData["status_previous"] = 0;
            $verificatioLogData["status_now"] = 2;
            $verificatioLogData["remark"] = "已领取发票";
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