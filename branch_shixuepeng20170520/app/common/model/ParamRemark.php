<?php
/**
 * Created by PhpStorm.
 * User: erin
 * Date: 2017/9/8
 */

namespace app\common\model;
use app\common\model\Base;


class ParamRemark extends Base
{
    public function __construct($corp_id = null)
    {
        $this->table = config('database.prefix').'param_remark';
        parent::__construct($corp_id);
    }
    public function getAllParam($con)
    {
        return $this->model->table($this->table)->where($con)->select();
    }
    public function getParamArray($con)
    {
        return $this->model->table($this->table)->where($con)->column("title","id");
    }

    public function addParam($data)
    {
        return $this->model->table($this->table)->insert($data);
    }

    public function setParam($id,$data)
    {
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }
    public function delParam($id){
        return $this->model->table($this->table)->where('id',$id)->delete();

    }

}