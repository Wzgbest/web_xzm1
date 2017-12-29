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
use app\crm\model\CustomerContact as CustomerContactModel;
use app\crm\model\SaleChance;
use app\crm\model\CustomerTrace;
use app\crm\model\CustomerTrace as CustomerTraceModel;
use app\common\model\ParamRemark;

class CustomerContact extends Initialize{
    public function index(){
        return "/crm/customer_contact/index";
    }
    protected function _showCustomerContact(){
        $customer_id = input('customer_id',0,'int');
        if(!$customer_id){
            $this->error("参数错误！");
        }
        $this->assign("customer_id",$customer_id);
        $this->assign("fr",input('fr'));
        $customerM = new CustomerContactModel($this->corp_id);
        $customerContactData = $customerM->getAllCustomerContactsByCustomerId($customer_id);
        $this->assign("customer_contact",$customerContactData);
        $customerM = new CustomerContactModel($this->corp_id);
        $customerData = $customerM->getCustomerContactCount($customer_id);
        $this->assign("customer_contact_num",$customerData);
        $customerM = new SaleChance($this->corp_id);
        $customerData = $customerM->getSaleChanceCount($customer_id);
        $this->assign("sale_chance_num",$customerData);
        $customerM = new CustomerTrace($this->corp_id);
        $customerData = $customerM->getCustomerTraceCount($customer_id);
        $this->assign("customer_trace_num",$customerData);
    }
    public function show(){
        $this->_showCustomerContact();
        return view();
    }
    public function add_page(){
        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $con['add_man']=array('in',array('0',$uid));
        $paramModel=new ParamRemark($this->corp_id);
        $param_list = $paramModel->getAllParam($con);//标签备注列表
        $this->assign("param_list",$param_list);

        $this->assign("fr",input('fr'));
        $this->assign("customer_id",input('customer_id',0,"int"));
        return view();
    }
    protected function _showCustomer(){
        $id = input('id',0,'int');
        if(!$id){
            $this->error("参数错误！");
        }
        $this->assign("id",$id);

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $con['add_man']=array('in',array('0',$uid));
        $paramModel=new ParamRemark($this->corp_id);
        $param_list = $paramModel->getAllParam($con);//标签备注列表
        $this->assign("param_list",$param_list);

        $this->assign("fr",input('fr'));
        $customerM = new CustomerContactModel($this->corp_id);
        $customer_contactData = $customerM->getCustomerContact($id);
        $this->assign("customer_contact",$customer_contactData);
    }
    public function edit_page(){
        $this->_showCustomer();
        return view();
    }
    public function table(){
        $result = ['status'=>0 ,'info'=>"获取联系人时发生错误！"];
        $customer_id = input('customer_id',0,'int');
        if(!$customer_id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $customerContactM = new CustomerContactModel($this->corp_id);
            $customerContactData = $customerContactM->getAllCustomerContactsByCustomerId($customer_id);
            $result['data'] = $customerContactData;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取联系人成功！";
        return json($result);
    }
    public function get(){
        $result = ['status'=>0 ,'info'=>"获取联系人时发生错误！"];
        $id = input('id',0,'int');
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $customerContactM = new CustomerContactModel($this->corp_id);
            $customerContactData = $customerContactM->getCustomerContact($id);
            $result['data'] = $customerContactData;
        }catch (\Exception $ex){
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取联系人成功！";
        return json($result);
    }
    protected function _getCustomerContactForInput($mode){
        if($mode){
            $userinfo = get_userinfo();
            $uid = $userinfo["userid"];
            $customerContact['create_time'] = time();
            $customerContact['create_user'] = $uid;
            $customerContact['customer_id'] = input('customer_id',0,'int');
        }
        // add customer contact page
        $customerContact['contact_name'] = input('contact_name','','string');
        $customerContact['phone_first'] = input('phone_first','','string');
        $customerContact['sex'] = input('sex',1,'int');

        $customerContact['phone_second'] = input('phone_second','','string');
        $customerContact['phone_third'] = input('phone_third','','string');

        $customerContact['email'] = input('email','','string');
        $customerContact['qqnum'] = input('qqnum','','string');
        $customerContact['wechat'] = input('wechat','','string');

        $customerContact['structure'] = input('structure','','string');
        $customerContact['occupation'] = input('occupation','','string');
        $customerContact['key_decide'] = input('key_decide',0,'int');
        $customerContact['deal_capability'] = input('deal_capability',0,'int');
        $customerContact['introducer'] = input('introducer','','string');
        $customerContact['close_degree'] = input('close_degree',0,'int');
        $customerContact['birthday'] = input('birthday',0,'strtotime');
        $customerContact['hobby'] = input('hobby','','string');
        $customerContact['remark'] = input('remark','','string');

        return $customerContact;
    }
    public function add(){
        $result = ['status'=>0 ,'info'=>"新建联系人时发生错误！"];
        $customerContact = $this->_getCustomerContactForInput("all");
        $validate_result = $this->validate($customerContact,'customer_contact');
        //验证字段
        if(true !== $validate_result){
            $result["info"] = $validate_result;
            return json($result);
        }
        $customerContactM = new CustomerContactModel($this->corp_id);

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();
        $table = 'customer_contact';

        $customersTrace["add_type"] = 0;
        $customersTrace["operator_type"] = 0;
        $customersTrace["operator_id"] = $uid;
        $customersTrace["create_time"] = $now_time;
        $customersTrace["customer_id"] = $customerContact['customer_id'];
        $customersTrace["db_table_name"] = $table;
        $customersTrace["db_field_name"] = "id";
        $customersTrace["old_value"] = 0;
        $customersTrace["new_value"] = 0;
        $customersTrace["value_type"] = "";
        $customersTrace["option_name"] = "添加了";
        $customersTrace["sub_name"] = "";
        $customersTrace["item_name"] = "新联系人";
        $customersTrace["from_name"] = "";
        $customersTrace["link_name"] = "";
        $customersTrace["to_name"] = $customerContact["contact_name"];
        $customersTrace["status_name"] = '';
        $customersTrace["remark"] = '';

        try{
            $customerContactM->link->startTrans();
            $customerContactId = $customerContactM->addCustomerContact($customerContact);

            $customerM = new CustomerTraceModel($this->corp_id);
            $customersTrace["new_value"] = $customerContactId;
            //var_exp($customersTrace,'$customersTrace',1);
            $customerTraceflg = $customerM->addSingleCustomerMessage($customersTrace);
            if(!$customerTraceflg){
                exception('提交客户跟踪数据失败!');
            }

            $customerContactM->link->commit();
            $result['data'] = $customerContactId;
        }catch (\Exception $ex){
            $customerContactM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "新建联系人成功！";
        return json($result);
    }
    public function getUpdateItemNameAndType(){
        $itemName["contact_name"] = ["联系人姓名"];
        $itemName["sex"] = ["性别","getSexName"];
        $itemName["phone_first"] = ["首要电话"];
        $itemName["phone_second"] = ["备用电话"];
        $itemName["phone_third"] = ["次用电话"];
        $itemName["email"] = ["邮箱"];
        $itemName["qqnum"] = ["QQ"];
        $itemName["wechat"] = ["微信"];
        $itemName["structure"] = ["所在部门"];
        $itemName["occupation"] = ["职位"];
        $itemName["key_decide"] = ["是否是关键决策人","getYesNoName"];
        $itemName["deal_capability"] = ["决策能力","getDealCapabilityName"];
        $itemName["introducer"] = ["客户介绍人"];
        $itemName["close_degree"] = ["亲密度","getCloseDegreeName"];
        $itemName["birthday"] = ["生日","day_format"];
        $itemName["hobby"] = ["爱好"];
        $itemName["remark"] = ["备注"];
        return $itemName;
    }
    public function update(){
        $result = ['status'=>0 ,'info'=>"保存联系人时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $customerContact = $this->_getCustomerContactForInput(0);
        $validate_result = $this->validate($customerContact,'customer_contact');
        //验证字段
        if(true !== $validate_result){
            $result["info"] = $validate_result;
            return json($result);
        }
        $customerContactM = new CustomerContactModel($this->corp_id);

        $userinfo = get_userinfo();
        $uid = $userinfo["userid"];
        $now_time = time();
        $customerContactOldData = $customerContactM->getCustomerContact($id);
        if(empty($customerContactOldData)){
            $result['info'] = "未找到联系人！";
            return json($result);
        }
        //var_exp($customerContactOldData,'$customerContactOldData');
        //var_exp($customerContact,'$customerContact');
        $updateItemName = $this->getUpdateItemNameAndType();
        //var_exp($updateItemName,'$updateItemName');
        $customerContactIntersertData = array_intersect_key($customerContactOldData,$customerContact);
        $customerContactIntersertData = array_intersect_key($customerContactIntersertData,$updateItemName);
        //unset($customerContactIntersertData["update_time"]);
        //var_exp($customerContactIntersertData,'$customerContactIntersertData');
        $customerContactDiffData = array_diff_assoc($customerContactIntersertData,$customerContact);
        //var_exp($customerContactDiffData,'$customerContactDiffData',1);
        $table = 'customer_contact';
        $customersTraces = [];
        foreach ($customerContactDiffData as $key=>$customerContactDiff){
            $customersTrace = createCustomersTraceItem(
                $uid,
                $now_time,
                $table,
                $customerContactOldData["customer_id"],
                $key,
                $customerContactOldData,
                $customerContact,
                $updateItemName,
                $customerContactOldData["contact_name"]
            );
            $customersTraces[] = $customersTrace;
        }
        //var_exp($customersTraces,'$customersTraces',1);

        try{
            $customerContactM->link->startTrans();
            $customerContactFlg = $customerContactM->setCustomerContact($id,$customerContact);

            if(!empty($customersTraces)){
                $customerM = new CustomerTraceModel($this->corp_id);
                $customerTraceflg = $customerM->addMultipleCustomerMessage($customersTraces);
                if(!$customerTraceflg){
                    exception('提交客户跟踪数据失败!');
                }
            }

            $customerContactM->link->commit();
            $result['data'] = $customerContactFlg;
        }catch (\Exception $ex){
            $customerContactM->link->rollback();
            $result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "保存联系人成功！";
        return json($result);
    }
}