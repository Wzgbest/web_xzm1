<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\systemsetting\model;

use app\common\model\Base;

class BusinessFlow extends Base{
    protected $dbprefix;
    public function __construct(){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'business_flow_setting';
        parent::__construct();
    }

    public function getAllBusinessFlow(){
        $businessFlowSettingList = $this->model
            ->table($this->table)
            ->order("id desc")
            ->field("*")
            ->select();
        foreach ($businessFlowSettingList as &$businessFlowSetting){
            $businessFlowSetting["set_to_role_arr"] = explode(",",$businessFlowSetting["set_to_role"]);
        }
        return $businessFlowSettingList;
    }

    public function getAllBusinessFlowName(){
        $businessFlowSettingNameList = $this->model
            ->table($this->table)
            ->order("id desc")
            ->column("business_flow_name","id");
        return $businessFlowSettingNameList;
    }

    public function getAllBusinessFlowByuserId($user_id){
        //TODO 根据用户和所属部门来查询业务流
        $businessFlowSettingList = $this->model
            ->table($this->table)
            ->order("id desc")
            ->field("*")
            ->select();
        foreach ($businessFlowSettingList as &$businessFlowSetting){
            $businessFlowSetting["set_to_role_arr"] = explode(",",$businessFlowSetting["set_to_role"]);
        }
        return $businessFlowSettingList;
    }
    /**
     * 查询业务流设置
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 业务流筛选条件
     * @param $order string 排序
     * @return array|false
     * @throws \think\Exception
     */
    public function getBusinessFlowSetting($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $businessFlowSettingList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('*')//TODO field list
            ->select();
        if($num==1&&$page==0&&$businessFlowSettingList){
            $businessFlowSettingList = $businessFlowSettingList[0];
        }
        return $businessFlowSettingList;
    }

    /**
     * 添加单个业务流设置
     * @param $data
     * @return int|string
     */
    public function addBusinessFlowSetting($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 根据业务流设置id修改业务流设置
     * @param $id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setBusinessFlowSetting($id,$data){
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 删除业务流设置,并返回结果
     * @param $map array 业务流筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delBusinessFlowSetting($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return ['res'=>$b ,'error'=>"0"];
    }
}