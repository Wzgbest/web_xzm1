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

class BusinessFlowItemLink extends Base{
    protected $dbprefix;
    public function __construct(){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'business_flow_item_link';
        parent::__construct();
    }

    public function getAllBusinessFlowItemLink($order="order_num asc"){
        $order = "bfil.".$order;
        $businessFlowItemLinkList = $this->model->table($this->table)->alias('bfil')
            ->join($this->dbprefix.'business_flow_item bfi','bfi.id = bfil.item_id',"LEFT")
            ->order($order)
            ->field("bfil.*,bfi.item_name,bfi.have_verification,bfi.verification_name")
            ->select();
        return $businessFlowItemLinkList;
    }

    public function getItemLinkById($businessFlowId,$order="order_num asc"){
        $order = "bfil.".$order;
        $map['bfil.setting_id'] = $businessFlowId;
        $businessFlowItemLinkList = $this->model->table($this->table)->alias('bfil')
            ->join($this->dbprefix.'business_flow_item bfi','bfi.id = bfil.item_id',"LEFT")
            ->where($map)
            ->order($order)
            ->field("bfil.*,bfi.item_name,bfi.have_verification,bfi.verification_name")
            ->select();
        return $businessFlowItemLinkList;
    }

    public function findItemLinkByItemId($businessFlowId,$businessFlowItemId){
        $map['bfil.setting_id'] = $businessFlowId;
        $map['bfil.item_id'] = $businessFlowItemId;
        $businessFlowItemLink = $this->model->table($this->table)->alias('bfil')
            ->join($this->dbprefix.'business_flow_item bfi','bfi.id = bfil.item_id',"LEFT")
            ->where($map)
            ->field("bfil.*,bfi.item_name,bfi.have_verification,bfi.verification_name")
            ->find();
        return $businessFlowItemLink;
    }
    /**
     * 查询业务流项目
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 业务流筛选条件
     * @param $order string 排序
     * @return array|false
     * @throws \think\Exception
     */
    public function getBusinessFlowItemLink($num=10,$page=0,$map=null,$order="order_num asc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $order = "bfil.".$order;
        $businessFlowItemLinkList = $this->model->table($this->table)->alias('bfil')
            ->join($this->dbprefix.'business_flow_item bfi','bfi.id = bfil.item_id',"LEFT")
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('bfil.*,bfi.item_name,bfi.have_verificationbfi.verification_name')
            ->select();
        if($num==1&&$page==0&&$businessFlowItemLinkList){
            $businessFlowItemLinkList = $businessFlowItemLinkList[0];
        }
        return $businessFlowItemLinkList;
    }

    public function getAllBusinessFlowNameAndDefault(){
        $businessFlowSettingNameList = $this->model->table($this->table)->alias('bfil')
            ->join($this->dbprefix.'business_flow_item bfi','bfi.id = bfil.item_id',"LEFT")
            ->join($this->dbprefix.'business_flow_setting bfs','bfs.id = bfil.setting_id',"LEFT")
            ->where("order_num",1)
            ->table($this->table)
            ->order("bfs.id desc")
            ->field(["bfs.id","bfs.business_flow_name","bfi.id item_id","bfi.item_name"])
            ->select();
        return $businessFlowSettingNameList;
    }

    public function getBusinessFlowSettingByRoleIds($role_ids){
        if(empty($role_ids)){
            return [];
        }
        $map["handle_1|handle_2|handle_3|handle_4|handle_5"] = ["in",$role_ids];
        return$this->model->table($this->table)
            ->where($map)
            ->field('*')//TODO field list
            ->select();
    }

    /**
     * 添加单个业务流项目
     * @param $data
     * @return int|string
     */
    public function addBusinessFlowItemLink($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 添加多个业务流项目
     * @param $datas
     * @return int|string
     */
    public function addMultipleItemLink($datas){
        return $this->model->table($this->table)
            ->field("setting_id,item_id,order_num,handle_1,handle_2,handle_3,handle_4,handle_5,handle_6")
            ->insertAll($datas);
    }

    /**
     * 根据业务流项目id修改业务流项目
     * @param $id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setBusinessFlowItemLink($id,$data){
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 删除业务流项目,并返回结果
     * @param $map array 业务流筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delBusinessFlowItemLink($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return ['res'=>$b ,'error'=>"0"];
    }
}