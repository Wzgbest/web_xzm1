<?php
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Role as RoleModel;
use app\systemsetting\model\BusinessFlow as BusinessFlowModel;
use app\systemsetting\model\BusinessFlowItem;
use app\systemsetting\model\BusinessFlowItemLink;

class BusinessFlow extends Initialize{
    protected $_businessFlowModel = null;
    protected $handle_max = 6;
    public function __construct(){
        parent::__construct();
        $this->_businessFlowModel = new BusinessFlowModel($this->corp_id);
    }
    public function index(){
        $business_flows = $this->_businessFlowModel->getAllBusinessFlow();
        //var_exp($business_flows,'$business_flows',1);
        $this->assign('listdata',$business_flows);
        $role_ids = [];
        $role_ids_arr = array_column($business_flows, 'set_to_role');
        foreach ($role_ids_arr as $id_str){
            $id_arr = explode(",",$id_str);
            if($id_arr){
                $role_ids = array_merge($role_ids,$id_arr);
            }
        }
        $role_ids = array_filter($role_ids);
        $role_ids = array_unique($role_ids);
        $roleM = new RoleModel($this->corp_id);
        $roleName = $roleM->getRoleName($role_ids);
        $this->assign("role_name",$roleName);
        return view();
    }
    public function add_page(){
        $business_flow_setting = [
            "id"=>"0",
            "business_flow_name"=>"",
            "set_to_role"=>"",
            "set_to_role_arr"=>[],
        ];
        $this->assign("business_flow_setting",$business_flow_setting);
        $businessFlowItemM = new BusinessFlowItem($this->corp_id);
        $businessFlowItems = $businessFlowItemM->getAllBusinessFlowItem("id asc");
        $this->assign('business_flow_items',$businessFlowItems);
        $this->assign('items_json',json_encode($businessFlowItems));
        $businessFlowItemLinks = [];
        $this->assign('business_flow_item_links',$businessFlowItemLinks);
        $this->assign('links_json',json_encode($businessFlowItemLinks));
        $roleM = new RoleModel($this->corp_id);
        $roles = $roleM->getAllRole();
        $this->assign('roles',$roles);
        $this->assign('roles_json',json_encode($roles));
        $this->assign('handle_max',$this->handle_max);
        $this->assign("url",url("add"));
        return view("edit_page");
    }

    public function edit_page(){
        $id = input("id");
        if(!$id){
            $this->error("参数错误!");
        }
        $map["id"] = $id;
        $business_flow_setting = [];
        try{
            $business_flow_setting = $this->_businessFlowModel->getBusinessFlowSetting(1,0,$map,"");
            $business_flow_setting["set_to_role_arr"] = explode(",",$business_flow_setting["set_to_role"]);
            //var_exp($business_flow_setting,'$business_flow_setting',1);
            $this->assign("business_flow_setting",$business_flow_setting);
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $businessFlowItemM = new BusinessFlowItem($this->corp_id);
        $businessFlowItems = $businessFlowItemM->getAllBusinessFlowItem("id asc");
        $this->assign('business_flow_items',$businessFlowItems);
        $this->assign('items_json',json_encode($businessFlowItems));
        $businessFlowItemLinkM = new BusinessFlowItemLink($this->corp_id);
        $businessFlowItemLinks = $businessFlowItemLinkM->getItemLinkById($id);
        $this->assign('business_flow_item_links',$businessFlowItemLinks);
        $this->assign('links_json',json_encode($businessFlowItemLinks));
        $roleM = new RoleModel($this->corp_id);
        $roles = $roleM->getAllRole();
        $this->assign('roles',$roles);
        $this->assign('roles_json',json_encode($roles));
        $this->assign('handle_max',$this->handle_max);
        $this->assign("url",url("update",["id"=>$id]));
        return view("edit_page");
    }

    protected function _getBusinessFlowSettingForInput(){
        $businessFlowSetting['business_flow_name'] = input('business_flow_name');

        $set_to_role_arr = input('set_to_role/a');
        $set_to_role_arr = array_map("intval",$set_to_role_arr);
        $set_to_role_arr = array_filter($set_to_role_arr);
        $set_to_role_arr = array_unique($set_to_role_arr);
        $zero_flg = true;
        do{
            $zero_flg = array_search(0,$set_to_role_arr);
            if($zero_flg){
                unset($set_to_role_arr[$zero_flg]);
            }
        }while($zero_flg);
        $businessFlowSetting['set_to_role'] = implode(",",$set_to_role_arr);

        return $businessFlowSetting;
    }

    protected function _getBusinessFlowItemLinkForInput($link_arr,$id){
        $link_data=[];
        foreach($link_arr as $link){
            unset($link["id"]);
            unset($link["item_name"]);
            unset($link["have_verification"]);
            unset($link["verification_name"]);
            $link["setting_id"] = $id;
            $link_data[] = $link;
        }
        //var_exp($link_data,'$link_data');
        return $link_data;
    }

    public function get(){
        $result = ['status'=>0 ,'info'=>"获取工作流设置时发生错误！"];
        $id = input("id");
        if(!$id){
            $this->error("参数错误!");
        }
        $map["id"] = $id;
        $business_flow_info = [];
        try{
            $business_flow_setting = $this->_businessFlowModel->getBusinessFlowSetting(1,0,$map,"");
            //var_exp($business_flow_setting,'$business_flow_setting',1);
            $business_flow_info["id"] = $id;
            $business_flow_info["name"] = $business_flow_setting["business_flow_name"];
            $businessFlowItemLinkM = new BusinessFlowItemLink($this->corp_id);
            $businessFlowItemLinks = $businessFlowItemLinkM->getItemLinkById($id);
            $business_flow_info['business_flow_item_links']=$businessFlowItemLinks;
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        $result["status"] = 1;
        $result["info"] = "获取工作流设置成功!";
        $result["data"] = $business_flow_info;
        return json($result);
    }

    public function add(){
        $result = ['status'=>0 ,'info'=>"添加工作流设置时发生错误！"];
        $businessFlowSetting = $this->_getBusinessFlowSettingForInput();
        $link_json = input('link_json');
        $link_arr = json_decode($link_json,true);
        //var_exp($businessFlowSetting,'$businessFlowSetting');
        //var_exp($link_arr,'$link_arr');
        if(empty($link_arr)){
            $result['info'] = "没有具体的业务流程！";
            return json($result);
        }
        $validate_result = $this->validate($businessFlowSetting,'BusinessFlowSetting');
        //验证字段
        if(true !== $validate_result){
            $result['info'] = $validate_result;
            return json($result);
        }
        $this->_businessFlowModel->link->startTrans();
        try{
            $businessFlowSettingAddFlg = $this->_businessFlowModel->addBusinessFlowSetting($businessFlowSetting);
            $result['data'] = $businessFlowSettingAddFlg;
            $businessFlowItemLinkM = new BusinessFlowItemLink($this->corp_id);
            $link_data = $this->_getBusinessFlowItemLinkForInput($link_arr,$businessFlowSettingAddFlg);
            //var_exp($link_data,'$link_data',1);
            $itemLinksAddFlg = $businessFlowItemLinkM->addMultipleItemLink($link_data);
            if(!$itemLinksAddFlg){
                exception("添加业务流程失败！");
            }
            $this->_businessFlowModel->link->commit();
        }catch (\Exception $ex){
            $this->_businessFlowModel->link->rollback();
            $result['info'] = '添加业务流失败!';
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "添加成功！";
        return json($result);
    }

    public function update(){
        $result = ['status'=>0 ,'info'=>"更新工作流设置时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $businessFlowSetting = $this->_getBusinessFlowSettingForInput();
        $link_json = input('link_json');
        $link_arr = json_decode($link_json,true);
        //var_exp($businessFlowSetting,'$businessFlowSetting');
        //var_exp($link_arr,'$link_arr',1);
        if(empty($link_arr)){
            $result['info'] = "没有具体的业务流程！";
            return json($result);
        }
        $validate_result = $this->validate($businessFlowSetting,'BusinessFlowSetting');
        //验证字段
        if(true !== $validate_result){
            $result['info'] = $validate_result;
            return json($result);
        }
        $this->_businessFlowModel->link->startTrans();
        try{
            $businessFlowSettingUpdateFlg = $this->_businessFlowModel->setBusinessFlowSetting($id,$businessFlowSetting);
            $businessFlowItemLinkM = new BusinessFlowItemLink($this->corp_id);
            $link_data = $this->_getBusinessFlowItemLinkForInput($link_arr,$id);
            //var_exp($link_data,'$link_data',1);
            //$old_links = $businessFlowItemLinkM->getItemLinkById($id);
            //var_exp($old_links,'$old_links',1);
            $itemLinksDelFlg = $businessFlowItemLinkM->delBusinessFlowItemLink(["setting_id"=>$id]);
            if(!$itemLinksDelFlg){
                exception("更新业务流程条目失败！");
            }
            $itemLinksAddFlg = $businessFlowItemLinkM->addMultipleItemLink($link_data);
            if(!$itemLinksAddFlg){
                exception("更新业务流程项目失败！");
            }
            $this->_businessFlowModel->link->commit();
        }catch (\Exception $ex){
            $this->_businessFlowModel->link->rollback();
            $result['info'] = '更新业务流失败!';
            //$result['info'] = $ex->getMessage();
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "更新成功！";
        return json($result);
    }

    public function del(){
        $result = ['status'=>0 ,'info'=>"删除工作流设置时发生错误！"];
        $ids = input("ids");
        if(!$ids){
            $result['info'] = "参数错误！";
            return json($result);
        }
        $ids_arr = explode(",",$ids);
        $map["id"] = array("in",$ids_arr);
        try{
            $businessFlowSettingDelFlg = $this->_businessFlowModel->delBusinessFlowSetting($map);
            if(!$businessFlowSettingDelFlg){
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