<?php
namespace app\verification\controller;

use app\common\controller\Initialize;

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
            $SaleOrderContractsData = [];//xxx($num,$p,$filter,$field,$order,$direction);
            $this->assign("list_data",$SaleOrderContractsData);
            $customers_count = 0;//xxx($filter);
            $this->assign("count",$customers_count);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $max_page = ceil($customers_count/$num);
        $userinfo = get_userinfo();
        $truename = $userinfo["truename"];
        $this->assign("p",$p);
        $this->assign("num",$num);
        $this->assign("filter",$filter);
        $this->assign("max_page",$max_page);
        $this->assign("truename",$truename);
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
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $update_flg = true;
        if(!$update_flg){
            $result['info'] = "通过发票申请失败！";
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
        $update_flg = false;
        if(!$update_flg){
            $result['info'] = "驳回发票申请失败！";
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
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='已领取发票开发中!';
        return $result;
    }
    public function invalid(){
        $result = ['status'=>0 ,'info'=>"作废发票时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $result['status']=1;
        $result['info']='作废发票开发中!';
        return $result;
    }
}