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
use app\common\model\Structure as StructureModel;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;

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
        $order = input("order","id","string");
        $direction = input("direction","desc","string");
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $filter = $this->_getCustomerFilter([]);
        $field = $this->_getCustomerField([]);
        //$filter["employee_id"] = $uid; // 审核人
        $filter["status"] = 1;
        try{
            $saleChanceM = new SaleOrderContractModel($this->corp_id);
            $SaleOrderContractsData = $saleChanceM->getAllSaleOrderContractByPage($num,$p,$filter,$field,$order,$direction);
            $this->assign("list_data",$SaleOrderContractsData);
            $customers_count = $saleChanceM->getAllSaleChanceCount($filter);
            $this->assign("count",$customers_count);
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
        $result = ['status'=>0 ,'info'=>"通过成单申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $saleOrderContract = $saleOrderContractM->getSaleOrderContract($id);
        //var_exp($saleOrderContract,'$saleOrderContract',1);
        $saleOrderContractStatus = $saleOrderContract["handle_status"];
        if(
            $saleOrderContractStatus!=6 &&
            !empty($saleOrderContract["handle_".($saleOrderContractStatus+1)])
        ){
            //还有下一步审批,转为下一个人审批
            $applied_data["handle_status"] = $saleOrderContractStatus+1;
            $saleOrderContractFlg = $saleOrderContractM->setSaleOrderContract($id,$applied_data);
            if(!$saleOrderContractFlg){
                $result['info'] = "移交审批失败！";
                return json($result);
            }
        }else{
            //最后一步审批
            $saleOrderContractFlg = $saleOrderContractM->approvedSaleOrderContract($id);
            if(!$saleOrderContractFlg){
                $result['info'] = "审批失败！!";
                return json($result);
            }
        }
        $result['status']=1;
        $result['info']='通过成单申请成功!';
        return $result;
    }
    public function rejected(){
        $result = ['status'=>0 ,'info'=>"驳回成单申请时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $saleOrderContractM = new SaleOrderContractModel($this->corp_id);
        $update_flg = $saleOrderContractM->rejectedSaleOrderContract($id);
        if(!$update_flg){
            $result['info'] = "驳回成单申请失败！";
            return json($result);
        }
        $result['status']=1;
        $result['info']='驳回成单申请成功!';
        return $result;
    }
}
