<?php
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Business;

class BusinessFlow extends Initialize{
    public function index(){
        $business_flows = [];
        //var_exp($business_flows,'$business_flows',1);
        $this->assign('listdata',$business_flows);
        return view();
    }
    public function add_page(){
        return view("edit_page");
    }

    public function edit_page(){
        return view("edit_page");
    }
}