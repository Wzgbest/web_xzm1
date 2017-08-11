<?php
/**
 * Created by messhair
 * Date: 17-3-11
 */
namespace app\huanxin\model;

use app\common\model\Base;

class RedEnvelope extends Base
{
    protected $dbprefix;
    public function __construct($corp_id =null)
    {
        $this->table = config('database.prefix').'red_envelope';
        parent::__construct($corp_id);
        $this->dbprefix = config('database.prefix');
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
        return $this->model->table($this->table)->alias('re')
            ->join(config('database.prefix').'employee e','re.took_user = e.id','left')
            ->field('re.id,re.redid,re.fromuser,re.money,re.total_money,re.is_token,re.create_time,re.took_time,re.took_user,re.took_time,e.telephone took_telephone,e.truename as took_user_name')
            ->where('re.redid',$red_id)
            ->where('re.is_token','<>',2)
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
        return $this->model->table($this->table)->alias('re')
            ->join(config('database.prefix').'employee e','re.took_user = e.id')
            ->field('re.redid,re.money,re.total_money,re.took_user,re.took_time,e.telephone took_telephone,e.truename as took_user_name')
            ->where('re.redid',$red_id)
            ->where('re.is_token',1)
            ->select();
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
     * 取出已被领取的红包
     * @param $red_id
     * @param $user
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function fetchedRedEnvelope($red_id,$user_id,$user_phone,$time)
    {
        $map["redid"] = $red_id;
        $map["is_token"] = 0;
        $data["is_token"] = 1;
        $data["took_user"] = $user_id;
        $data["took_telephone"] = $user_phone;
        $data["took_time"] = $time;
        return $this->model->table($this->table)
            ->where($map)
            ->limit(1)
            ->data($data)
            ->update();
    }

    /**
     * 红包数量
     * @param $user
     * @param $red_id
     * @return int|string
     */
    public function getUserRedCount($user,$red_id)
    {
        $map["redid"] = $red_id;
        $map["is_token"] = 1;
        $map["took_user"] = $user;
        return $this->model->table($this->table)->where($map)->count('id');
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

    /**
     * 根据红包red_id获取所有信息
     * @param $num int 每页数量
     * @param $page int 页码
     * @param $uid int 员工id
     * @param $map array 筛选条件
     * @param $order string 排序方式
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getMyRedEnvelope($num=10,$page=0,$uid,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = ($page-1)*$num;
        }
        $field = [
            're.id',
            'redid',
            'type',
            'fromuser as from_user',
            'e.telephone as from_telephone',
            '(case when fromuser = '.$uid.' then 0-total_money else money end) as money',
            'took_time',
            'is_token',
            're.create_time',
            'took_user',
            'took_telephone'
        ];
        return $this->model->table($this->table)->alias('re')
            ->join($this->dbprefix.'employee e','e.id = re.fromuser',"LEFT")
            ->where($map)
            ->where('fromuser|took_user',$uid)
            //->whereOr('is_token','<>',2)
            ->order($order)
            ->limit($offset,$num)
            ->field($field)
            ->select();
    }
}