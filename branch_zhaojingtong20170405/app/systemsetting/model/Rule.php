<?php
/**
 * Created by PhpStorm.
 * User: erin
 * Date: 2017/11/6
 */

namespace app\systemsetting\model;

use app\common\model\Base;


class Rule extends Base{
    protected $dbprefix;
    public function __construct(){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'rule';
        parent::__construct();
    }
    /**
     * @param $data
     * @return int|string
     * 添加权限
     */
    public function addRule($data){
        return $this->model->table($this->table)->insertGetId($data);
    }
    /**
     * @param $data
     * @return int|string
     * 编辑权限
     */
    public function editRule($data){
        return $this->model->table($this->table)->update($data);
    }
    public function delRule($map,$data){
        return $this->model->table($this->table)->where($map)->update($data);

    }

    /**
     * @return false|\PDOStatement|string|\think\Collection
     * 第一级的列表
     */
    public function getFirstLevelData(){
        $map['type']=array('neq','3');
        $map['status']=1;
        return $this->model->table($this->table)->field('id,pid,rule_title')->where($map)->select();
    }
    /**
     * @param $map
     * @param string $field
     * @param string $order
     * @param string $direction
     * @return false|\PDOStatement|string|\think\Collection
     * 所有的权限列表
     */
    public function getAllRuleList($map,$field='*',$order="id",$direction="desc"){
        $listOrder = [$order=>$direction];//聚合后排序
        return $this->model->table($this->table)->field($field)->where($map)->order($listOrder)->select();
    }
    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * 单条权限记录
     */
    public function getRuleInfo($map){
        return $this->model->table($this->table)->where($map)->find();
    }
}