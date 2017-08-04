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

class BusinessFlowItem extends Base{
    protected $dbprefix;
    public function __construct(){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'business_flow_item';
        parent::__construct();
    }

    public function getAllBusinessFlowItem($order="id desc"){
        $businessFlowItemList = $this->model
            ->table($this->table)
            ->order($order)
            ->field("*")
            ->select();
        return $businessFlowItemList;
    }

    public function getAllSelectBusinessFlowItem($order="id desc"){
        $map["type"] = 1;
        $map["status"] = 1;
        $businessFlowItemList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->field("*")
            ->select();
        return $businessFlowItemList;
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
    public function getBusinessFlowItem($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $businessFlowItemList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('*')//TODO field list
            ->select();
        if($num==1&&$page==0&&$businessFlowItemList){
            $businessFlowItemList = $businessFlowItemList[0];
        }
        return $businessFlowItemList;
    }

    /**
     * 添加单个业务流项目
     * @param $data
     * @return int|string
     */
    public function addBusinessFlowItem($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 根据业务流项目id修改业务流项目
     * @param $id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setBusinessFlowItem($id,$data){
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 删除业务流项目,并返回结果
     * @param $map array 业务流筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delBusinessFlowItem($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return ['res'=>$b ,'error'=>"0"];
    }
}