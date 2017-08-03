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

class BillSetting extends Base{
    protected $dbprefix;
    public function __construct(){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'bill_setting';
        parent::__construct();
    }

    public function getAllBill(){
        $billSettingList = $this->model
            ->table($this->table)
            ->order("id desc")
            ->field("*")
            ->select();
        return $billSettingList;
    }
    /**
     * 查询发票设置
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 发票筛选条件
     * @param $order string 排序
     * @return array|false
     * @throws \think\Exception
     */
    public function getBillSetting($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $billSettingList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('*')//TODO field list
            ->select();
        if($num==1&&$page==0&&$billSettingList){
            $billSettingList = $billSettingList[0];
        }
        return $billSettingList;
    }

    /**
     * 添加单个发票设置
     * @param $data
     * @return int|string
     */
    public function addBillSetting($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 获取单个发票设置
     * @param $id int 发票设置id
     * @return int|string
     */
    public function getBillSettingById($id){
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    /**
     * 根据发票设置id修改发票设置
     * @param $id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setBillSetting($id,$data){
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 删除发票设置,并返回结果
     * @param $map array 发票筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delBillSetting($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return ['res'=>$b ,'error'=>"0"];
    }
}