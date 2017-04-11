<?php
/**
 * Created by messhair
 * Date: 17-3-11
 */
namespace app\huanxin\model;

use app\common\model\Base;

class RedEnvelope extends Base
{
    public function __construct($corp_id)
    {
        $this->table = config('database.prefix').'red_envelope';
        parent::__construct($corp_id);
    }

    /**
     * 生成红包
     * @param $data
     * @return int|string
     */
    public function createRedId($data)
    {
        return $this->model->table($this->table)->insertAll($data);
    }

    /**
     * 根据红包red_id获取所有信息
     * @param $red_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRedInfoByRedId($red_id)
    {
        return $this->model->table($this->table)
            ->field('id,fromuser,money,took_time,is_token,create_time,took_user,total_money,took_telephone')
            ->where('redid',$red_id)
            ->where('is_token','<>',2)
            ->select();
    }
    /**
     * 验证是否已领取红包
     * @param $userid 用户id非电话
     * @param $red_id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function checkIfTook($userid,$red_id)
    {
        return $this->model->table($this->table)->where('redid',$red_id)->where('took_user',$userid)->find();
    }
    /**
     * 红包取出一个
     * @param $red_id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getOneRedId($red_id)
    {
        return $this->model->table($this->table)->where('redid',$red_id)->where('is_token',0)->find();
    }

    /**
     * 被领取的红包状态更改
     * @param $id 红包表 id
     * @param $data
     * @return int|string
     * @throws \think\Exception
     */
    public function setOneRedId($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 取出已被领取的红包
     * @param $red_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getFetchedRedList($red_id)
    {
        return $this->model->table($this->table)->alias('a')
            ->join(config('database.prefix').'employer b','a.took_user = b.id')
            ->field('a.redid,a.money,a.total_money,a.took_time,b.telephone,b.truename as took_user')
            ->where('a.redid',$red_id)->where('a.is_token',1)->select();
    }

    /**
     * 红包数量
     * @param $red_id
     * @return int|string
     */
    public function getRedCount($red_id)
    {
        return $this->model->table($this->table)->where('redid',$red_id)->count('id');
    }

    /**
     * 按红包redid查询所有超时未领取的红包
     * @param $red_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getOverTimeRedIdsFromRedId($red_id)
    {
        return $this->model->table($this->table)->field('id,redid,fromuser,money')->where('redid',$red_id)
            ->where('is_token',0)->select();
    }

    /**
     * 红包返还
     * @param $ids 红包id
     * @return int|string
     * @throws \think\Exception
     */
    public function setOverTimeRed($ids,$time)
    {
        return $this->model->table($this->table)->where("id in($ids)")->update(['is_token'=>2,'sendback_time'=>$time]);
    }

    /**
     * 按用户id查询超时未领取的红包
     * @param $userid 用户的id非tel
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getOverTimeRedIdsFromUserId($userid)
    {
        $dep_time = time()-config('red_envelope.overtime');
        return $this->model->table($this->table)
            ->where('fromuser',$userid)->where('is_token',0)
            ->where('create_time','<',$dep_time)
            ->select();
    }
}