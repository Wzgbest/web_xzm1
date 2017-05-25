<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\common\model\SearchCustomer as SearchCustomerModel;

class SearchCustomer extends Initialize{
    protected $_searchCustomerModel = null;
    public function __construct(){
        parent::__construct();
        $corp_id = get_corpid();
        $this->_searchCustomerModel = new SearchCustomerModel($corp_id);
    }

    public function index(){
        $uri = "crm/search_customer/index";
        return $this->fetch('index',["uri"=>$uri]);
    }

    public function table(){
        $result = ['status'=>0 ,'info'=>"查询搜索客户时发生错误！"];
        $num = 10;
        $p = input("p");
        $p = $p?:1;
        $map["status"] = 1;
        try{
            $searchCustomers = $this->_searchCustomerModel->getSearchCustomer($num,$p,$map);
            $result['data'] = $searchCustomers['res'];
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }

    public function get(){
        $result = ['status'=>0 ,'info'=>"获取搜索客户时发生错误！"];
        $id = input("id");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $map["id"] = $id;
        $map["status"] = 1;
        try{
            $searchCustomers = $this->_searchCustomerModel->getSearchCustomer(1,0,$map,"");
            if($searchCustomers){
                $result["data"] = $searchCustomers['res'][0];
            }
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }

    public function repeat(){
        $result = ['status'=>0 ,'info'=>"获取搜索客户重复客户时发生错误！"];
        $id = input("id");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $repeat = $this->_searchCustomerModel->findRepeat($id);
            $result['data'] = $repeat['res'];
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "查询成功！";
        return json($result);
    }

    protected function _getSearchCustomerForInput(){
        $customer['customer_name'] = input('customer_name');
        $customer['phone'] = input('phone');

        $customer['contact_name'] = input('contact_name');
        $customer['industry'] = input('industry');
        $customer['com_adds'] = input('com_adds');
        $customer['website'] = input('website');

        /*
        $customer['customer_from'] = input('customer_from');
        $customer['customer_level'] = input('customer_level');
        $customer['niche'] = input('niche');
        $customer['est_amount'] = input('est_amount');
        $customer['comm_state'] = input('comm_state');
        */
        return $customer;
    }

    public function add(){
        $result = ['status'=>0 ,'info'=>"添加搜索客户时发生错误！"];
        $uid = session('userinfo.id');
        $customer = $this->_getSearchCustomerForInput();
        $customer['search_from'] = input('search_from');

        $customer['create_user'] = $uid;
        $customer['create_time'] = time();
        $customer['status'] = 1;

        try{
            $validate_result = $this->validate($customer,'SearchCustomer');
            //验证字段
            if(true !== $validate_result){
                $result['info'] = $validate_result;
                return json($result);
            }
            $searchCustomerAddFlg = $this->_searchCustomerModel->createSearchCustomer($customer);
            $result['data'] = $searchCustomerAddFlg['res'];
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "添加成功！";
        return json($result);
    }

    public function update(){
        $result = ['status'=>0 ,'info'=>"更新搜索客户时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $uid = session('userinfo.id');
        $customer = $this->_getSearchCustomerForInput();

        $map["id"] = $id;
        $map["create_user"] = $uid;
        $map["status"] = 1;
        try{
            $validate_result = $this->validate($customer,'SearchCustomer');
            //验证字段
            if(true !== $validate_result){
                $result['info'] = $validate_result;
                return json($result);
            }
            $searchCustomerUpdateFlg = $this->_searchCustomerModel->updateSearchCustomer($customer,$map);
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "更新成功！";
        return json($result);
    }

    public function del(){
        $result = ['status'=>0 ,'info'=>"删除搜索客户时发生错误！"];
        $ids = input("ids");
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $ids_arr = explode(",",$ids);
        $uid = session('userinfo.id');
        $map["id"] = array("in",$ids_arr);
        $map["create_user"] = $uid;
        $map["status"] = 1;
        try{
            $searchCustomerDelFlg = $this->_searchCustomerModel->delSearchCustomer($map);
            if(!$searchCustomerDelFlg['res']){
                return json($result);
            }
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "删除成功！";
        return json($result);
    }
}
