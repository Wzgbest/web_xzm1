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
    public function __construct($corp_id = null){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'call_record';
        parent::__construct($corp_id);
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
        $callRecordList = $this->model->table($this->table)->alias('cr')
            ->join($this->dbprefix.'customer c','cr.customer_id = c.id',"left")
            ->join($this->dbprefix.'customer_contact cc','cr.contactor_id = cc.id',"left")
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("cr.*,c.customer_name,cc.contact_name,'' as head_img_url")//TODO field list
            ->select();
        //var_exp($callRecordList,'$callRecordList',1);
        if($num==1&&$page==0&&$callRecordList){
            $callRecordList = $callRecordList[0];
        }
        return $callRecordList;
    }
}