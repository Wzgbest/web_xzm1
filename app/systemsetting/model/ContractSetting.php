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

class ContractSetting extends Base{
    protected $dbprefix;
    public function __construct(){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'contract_setting';
        parent::__construct();
    }

    public function getAllContract(){
        $contractSettingList = $this->model
            ->table($this->table)
            ->order("id desc")
            ->field("*")
            ->select();
        return $contractSettingList;
    }

    public function getAllContractName($contract_type_ids=null){
        $map = [];
        if($contract_type_ids){
            $map["id"] = ["in",$contract_type_ids];
        }
        $contractSettingList = $this->model
            ->table($this->table)
            ->where($map)
            ->order("id desc")
            ->column("contract_name","id");
        return $contractSettingList;
    }
    /**
     * 查询合同设置
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 合同筛选条件
     * @param $order string 排序
     * @return array|false
     * @throws \think\Exception
     */
    public function getContractSetting($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $contractSettingList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('*')//TODO field list
            ->select();
        if($num==1&&$page==0&&$contractSettingList){
            $contractSettingList = $contractSettingList[0];
        }
        return $contractSettingList;
    }

    /**
     * 添加单个合同设置
     * @param $data
     * @return int|string
     */
    public function addContractSetting($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 根据合同设置id获取合同设置
     * @param $id
     * @return int|string
     * @throws \think\Exception
     */
    public function getContractSettingById($id){
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    /**
     * 根据合同设置id修改合同设置
     * @param $id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setContractSetting($id,$data){
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 删除合同设置,并返回结果
     * @param $map array 合同筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delContractSetting($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return ['res'=>$b ,'error'=>"0"];
    }
}