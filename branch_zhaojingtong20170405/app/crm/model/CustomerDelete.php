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
class CustomerDelete extends Base
{
    protected $dbprefix;
    public function __construct($corp_id = null)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'customer';
        parent::__construct($corp_id);
    }

    /**
     * 删除客户
     * @param $cids array 客户id数组
     * @return int
     * @throws \think\Exception
     * created by blu10ph
     */
    public function moveInDelMultipleCustomer($cids){
        $flg = false;
        //TODO 写入客户删除表
        //$flg = $this->model->table($this->table)->where('id','in',$cids)->delete();
        return $flg;
    }

    /**
     * 恢复删除的客户
     * @param $cids array 客户id数组
     * @return int
     * @throws \think\Exception
     * created by blu10ph
     */
    public function moveOutDelMultipleCustomer($cids){
        $flg = false;
        //TODO 写入客户删除表
        //$flg = $this->model->table($this->table)->where('id','in',$cids)->delete();
        return $flg;
    }

    /**
     * 真丶物理删除客户
     * @param $cids array 客户id数组
     * @return int
     * @throws \think\Exception
     * created by blu10ph
     */
    public function delCustomer($cids)
    {
        return $this->model->table($this->table)->where('id','in',$cids)->delete();
    }
}