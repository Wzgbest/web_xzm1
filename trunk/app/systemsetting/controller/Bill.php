<?php
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\BillSetting as BillSettingModel;

class Bill extends Initialize{
    protected $_billSettingModel = null;
    public function __construct(){
        parent::__construct();
        $corp_id = get_corpid();
        $this->_billSettingModel = new BillSettingModel($corp_id);
    }
    public function index(){
        $bills = $this->_billSettingModel->getAllBill();
        //var_exp($bills,'$bills',1);
        $this->assign('listdata',$bills);
        return view();
    }
    public function add_page(){
        $billSetting = [
            "id"=>"0",
            "bill_type"=>"",
            "need_tax_id"=>"",
            "product_type"=>[],
            "bank_type"=>[],
            "max_bill"=>"",
            "handle_1"=>"",
            "handle_2"=>"",
            "handle_3"=>"",
            "handle_4"=>"",
            "handle_5"=>"",
            "handle_6"=>"",
            "create_bill_num_1"=>"",
            "create_bill_num_2"=>"",
            "create_bill_num_3"=>"",
            "create_bill_num_4"=>"",
            "create_bill_num_5"=>"",
            "create_bill_num_6"=>"",
        ];
        $this->assign("billSetting",$billSetting);
        $this->assign("url",url("add"));
        return view("edit_page");
    }

    public function edit_page(){
        $id = input("id");
        if(!$id){
            $this->error("参数错误!");
        }
        $map["id"] = $id;
        try{
            $billSetting = $this->_billSettingModel->getBillSetting(1,0,$map,"");
            $this->assign("billSetting",$billSetting);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $this->assign("url",url("update"));
        return view("edit_page");
    }

    protected function _getBillSettingForInput(){
        $billSetting['bill_type'] = input('bill_type');
        $billSetting['need_tax_id'] = input('need_tax_id',0,'int');
        $billSetting['max_bill'] = input('max_bill',0,'int');
        $billSetting['handle_1'] = input('handle_1',0,'int');
        $billSetting['handle_2'] = input('handle_2',0,'int');
        $billSetting['handle_3'] = input('handle_3',0,'int');
        $billSetting['handle_4'] = input('handle_4',0,'int');
        $billSetting['handle_5'] = input('handle_5',0,'int');
        $billSetting['handle_6'] = input('handle_6',0,'int');
        $billSetting['create_bill_num_1'] = input('create_bill_num_1',0,'int');
        $billSetting['create_bill_num_2'] = input('create_bill_num_2',0,'int');
        $billSetting['create_bill_num_3'] = input('create_bill_num_3',0,'int');
        $billSetting['create_bill_num_4'] = input('create_bill_num_4',0,'int');
        $billSetting['create_bill_num_5'] = input('create_bill_num_5',0,'int');
        $billSetting['create_bill_num_6'] = input('create_bill_num_6',0,'int');

        $product_type_arr = input('product_type/a');
        $product_type_arr = array_map("intval",$product_type_arr);
        $product_type_arr = array_filter($product_type_arr);
        $product_type_arr = array_unique($product_type_arr);
        $zero_flg = true;
        do{
            $zero_flg = array_search(0,$product_type_arr);
            if($zero_flg){
                unset($product_type_arr[$zero_flg]);
            }
        }while($zero_flg);
        $customerSetting['product_type'] = implode(",",$product_type_arr);

        $bank_type_arr = input('bank_type/a');
        $bank_type_arr = array_map("intval",$bank_type_arr);
        $bank_type_arr = array_filter($bank_type_arr);
        $bank_type_arr = array_unique($bank_type_arr);
        $zero_flg = true;
        do{
            $zero_flg = array_search(0,$bank_type_arr);
            if($zero_flg){
                unset($bank_type_arr[$zero_flg]);
            }
        }while($zero_flg);
        $customerSetting['bank_type'] = implode(",",$bank_type_arr);
        return $billSetting;
    }

    public function add(){
        $result = ['status'=>0 ,'info'=>"添加发票设置时发生错误！"];
        $billSetting = $this->_getBillSettingForInput();
        try{
            $validate_result = $this->validate($billSetting,'BillSetting');
            //验证字段
            if(true !== $validate_result){
                $result['info'] = $validate_result;
                return json($result);
            }
            $billSettingAddFlg = $this->_billSettingModel->addBillSetting($billSetting);
            $result['data'] = $billSettingAddFlg;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "添加成功！";
        return json($result);
    }

    public function update(){
        $result = ['status'=>0 ,'info'=>"更新发票设置时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $billSetting = $this->_getBillSettingForInput();
        try{
            $validate_result = $this->validate($billSetting,'BillSetting');
            //验证字段
            if(true !== $validate_result){
                $result['info'] = $validate_result;
                return json($result);
            }
            $billSettingUpdateFlg = $this->_billSettingModel->setBillSetting($id,$billSetting);
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "更新成功！";
        return json($result);
    }

    public function del(){
        $result = ['status'=>0 ,'info'=>"删除发票设置时发生错误！"];
        $ids = input("ids");
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $ids_arr = explode(",",$ids);
        $map["id"] = array("in",$ids_arr);
        try{
            $billSettingDelFlg = $this->_billSettingModel->delBillSetting($map);
            if(!$billSettingDelFlg){
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