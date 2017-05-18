<?php
namespace app\crm\controller;

use think\Controller;
use app\common\model\SearchCustomer as SearchCustomerModel;

class SearchCustomer extends Controller{
    protected $_searchCustomerModel = null;
    public function __construct(){
        parent::__construct();
        //session('userinfo.id',1);
        //session('userinfo.corp_id','sdzhongxun');
        $corp_id = get_corpid();
        $this->_searchCustomerModel = new SearchCustomerModel();
    }

    public function index(){
        $uri = "crm/search_customer/index";
        return $this->fetch('index',["uri"=>$uri]);
    }

    public function table(){
        $num = 10;
        $p = input("p");
        $map["status"] = 1;
        $result = $this->_searchCustomerModel->getSearchCustomer($num,$p,$map);
        return json_encode($result);
    }

    public function get(){
        $id = input("id");
        if(!$id){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"参数错误！"];
        }
        $map["id"] = $id;
        $map["status"] = 1;
        $result = $this->_searchCustomerModel->getSearchCustomer(1,0,$map,"");
        $result["res"] = $result["res"][0];
        return json_encode($result);
    }

    public function repeat(){
        $id = input("id");
        if(!$id){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"参数错误！"];
        }
        $result = $this->_searchCustomerModel->findRepeat($id);
        return json_encode($result);
    }

    public function add(){
        $uid = session('userinfo.id');
        $customer['search_from'] = input('search_from');

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

        $customer['create_user'] = $uid;
        $customer['create_time'] = time();
        $customer['status'] = 1;

        $result = $this->_searchCustomerModel->createSearchCustomer($customer);
        return json_encode($result);
    }

    public function update(){
        $id = input("id");
        if(!$id){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"参数错误！"];
        }
        $uid = session('userinfo.id');
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

        $map["id"] = $id;
        $map["create_user"] = $uid;
        $map["status"] = 1;
        $result = $this->_searchCustomerModel->updateSearchCustomer($customer,$map);
        return json_encode($result);
    }

    public function del(){
        $ids = input("ids");
        if(!$ids){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"参数错误！"];
        }
        $ids_arr = explode(",",$ids);
        $uid = session('userinfo.id');
        $map["id"] = array("in",$ids_arr);
        $map["create_user"] = $uid;
        $map["status"] = 1;
        $result = $this->_searchCustomerModel->delSearchCustomer($map);
        return json_encode($result);
    }
}
