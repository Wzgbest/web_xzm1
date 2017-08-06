<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\verification\model;

use app\common\model\Base;

class VerificatioLog extends Base{
    protected $dbprefix;
    public function __construct($corp_id){
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix') . 'verification_log';
        parent::__construct($corp_id);
    }

    /**
     * 查询记录
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 记录筛选条件
     * @param $field array 记录列筛选条件
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return array|false
     * @throws \think\Exception
     */
    public function getVerificatioLog($num=10,$page=0,$filter=null,$field=null,$order="ca.id",$direction="desc"){
        //分页
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }

        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $billList = $this->model->table($this->table)->alias('vl')
            ->join($this->dbprefix.'employee e','e.id = vl.create_user',"LEFT")
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('vl.*,e.truename as create_user_name')
            ->select();
        //var_exp($billList,'$billList',1);
        if($num==1&&$page==0&&$billList){
            $billList = $billList[0];
        }
        return $billList;
    }
    /**
     * 查询记录数量
     * @param $filter array 记录筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getVerificatioLogCount($filter=null){
        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        $billCount= $this->model->table($this->table)->alias('vl')
            ->where($map)
            ->count();
        return $billCount;
    }

    protected function _getMapByFilter($filter,$filter_column){
        $map = [];
        return $map;
    }

    public function addVerificatioLog($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    public function addMutipleVerificatioLog($datas){
        return $this->model->table($this->table)->insertAll($datas);
    }

}