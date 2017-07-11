<?php
/**
 * Created by: messhair
 * Date: 2017/5/9
 */
namespace app\common\model;

use app\common\model\Base;

class Structure extends Base
{
    protected $dbprefix;
    public function __construct($corp_id =null)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'structure';
        parent::__construct($corp_id);
    }

    /**
     * 获取所有部门
     * @return false|\PDOStatement|string|\think\Collection
     * created by messhair
     */
    public function getAllStructure()
    {
        return $this->model->table($this->table)->select();
    }

    /**
     * 获取单个部门信息
     * @param $struct_id int 部门id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by messhair
     */
    public function getStructureInfo($struct_id)
    {
        return $this->model->table($this->table)->where('id',$struct_id)->find();
    }

    /**
     * 获取单个部门信息
     * @param $struct_ids array 部门id
     * @return array|false|\PDOStatement|string|\think\Model
     * created by blu10ph
     */
    public function getStructureName($struct_ids)
    {
        return $this->model->table($this->table)->where('id',"in",$struct_ids)->column("struct_name","id");
    }

    /**
     *监察部门层级深度
     * @param $struct_pid int 部门id
     * @param $level_deep int 层级深度
     * @return array|false|\PDOStatement|string|\think\Model
     * created by blu10ph
     */
    public function checkStructureLevelDeep($struct_pid,$level_deep)
    {
        $query = $this->model->table($this->table)->alias('dl1');
        $field = ["dl1.id as id1"];
        for($i=2;$i<=$level_deep;$i++){
            $query = $query->join($this->dbprefix.'structure dl'.$i,'dl'.$i.'.id = dl'.($i-1).'.struct_pid');
            $field[]="dl".$i.".id as id".$i;
        }
        $level_info = $query
            ->where('dl1.id',$struct_pid)
            ->field($field)
            ->find();
        //var_exp($level_info,'$level_info',1);
        if($level_info && $level_info["id".$level_deep]){
            return false;
        }
        return true;
    }

    /**
     * 添加部门信息
     * @param $data
     * @return int|string
     * created by messhair
     */
    public function addStructure($data)
    {
        return $this->model->table($this->table)->insertGetId($data);
    }

    /**
     * 更新单个部门信息
     * @param $id int 部门id
     * @param $data array 数据信息
     * @return int|string
     * @throws \think\Exception
     * created by messhair
     */
    public function setStructure($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    /**
     * 删除部门
     * @param $id int 部门id
     * @return int
     * @throws \think\Exception
     * created by messhair
     */
    public function deleteStructure($ids)
    {
        return $this->model->table($this->table)->where('id','in',$ids)->delete();
    }
}