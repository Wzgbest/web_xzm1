<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\crm\model;

use app\common\model\Base;

class CallRecord extends Base{
    protected $dbprefix;
    public function __construct(){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'call_record';
        parent::__construct();
    }

    /**
     * 查询通话记录
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 客户筛选条件
     * @param $order string 排序
     * @return array|false
     * @throws \think\Exception
     */
    public function getCallRecord($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $searchCustomerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('*')//TODO field list
            ->select();
        if($num==1&&$page==0&&$searchCustomerList){
            $searchCustomerList = $searchCustomerList[0];
        }
        return $searchCustomerList;
    }
}