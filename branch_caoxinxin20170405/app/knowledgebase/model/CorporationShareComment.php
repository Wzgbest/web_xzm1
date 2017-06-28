<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\knowledgebase\model;

use app\common\model\Base;
use think\Db;

class CorporationShareCommentComment extends Base{
    protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'corporation_share';
        parent::__construct($corp_id);
    }

    /**
     * 获取评论
     * @param $uid int 用户id
     * @param $map array 评论筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     */
    public function getAllCorporationShareComment($uid,$map=null,$order="id desc"){
        $map["create_user"] = $uid;
        $map["status"]=1;
        $searchCustomerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->field(array("id","customer_name","phone","contact_name","industry","com_adds","website"))
            ->select();
        return ['res'=>$searchCustomerList ,'error'=>"0"];
    }

    /**
     * 获取评论
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 评论筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     */
    public function getCorporationShareComment($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $searchCustomerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field("*")//TODO
            ->select();
        return ['res'=>$searchCustomerList ,'error'=>"0"];
    }

    /**
     * 创建评论,并返回结果
     * @param $customer array 评论信息
     * @return array
     * @throws \think\Exception
     */
    public function createCorporationShareComment($customer){
        $customer_name = $customer['userid'];
        if(empty($customer_name)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"参数错误！"];
        }
        $b = $this->model->table($this->table)->insertGetId($customer);
        return ['res'=>$b ,'error'=>"0"];
    }

    /**
     * 更新评论,并返回结果
     * @param $customer array 评论信息
     * @param $map array 评论筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function updateCorporationShareComment($customer,$map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"更新参数错误！"];
        }
        $b = $this->model->table($this->table)->where($map)->data($customer)->save();
        return ['res'=>$b ,'error'=>"0"];
    }

    /**
     * 删除评论,并返回结果
     * @param $map array 评论筛选条件
     * @return array
     * @throws \think\Exception
     */
    public function delCorporationShareComment($map){
        if(empty($map)){
            return ['res'=>0 ,'error'=>"1" ,'msg'=>"缺少删除目标！"];
        }
        $b = $this->model->table($this->table)->where($map)->delete();
        return ['res'=>$b ,'error'=>"0"];
    }
}