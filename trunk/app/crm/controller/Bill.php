<?php
namespace app\crm\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\BillSetting as BillSettingModel;

class Bill extends Initialize{
    var $paginate_list_rows = 10;
    public function _initialize(){
        parent::_initialize();
        $this->paginate_list_rows = config("paginate.list_rows");
    }

    public function get_bill_setting(){
        $result = ['status'=>0 ,'info'=>"获取发票设置时发生错误！"];
        $id = input("id",0,"int");
        if(!$id){
            $result['info'] = "参数错误！";
            return json($result);
        }
        try{
            $billSettingModel = new BillSettingModel($this->corp_id);
            $contractSetting = $billSettingModel->getBillSettingById($id);
            if(empty($contractSetting)){
                exception("未找到发票设置!");
            }
            //TODO 角色对应的人,合同类型和对应的合同
            $result['data'] = $contractSetting;
        }catch (\Exception $ex){
            return json($result);
        }
        $result['status'] = 1;
        $result['info'] = "获取成功！";
        return json($result);
    }
}