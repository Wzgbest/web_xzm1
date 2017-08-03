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

class Contract extends Base
{
    protected $dbprefix;
    public function __construct($corp_id)
    {
        $this->dbprefix = config('database.prefix');
        $this->table = config('database.prefix').'contract_applied';
        parent::__construct($corp_id);
    }
    /**
     * 查询所有合同
     * @param $user_id int 用户id
     * @param $type int 合同类型
     * @param $status array 状态
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return array|false
     * @throws \think\Exception
     */
    public function getAllContract($user_id=null,$status=[],$type=null,$order="ca.id",$direction="desc"){
        //筛选
        $map = [];
        if($user_id){
            $map["ca.employee_id"] = $user_id;
        }
        if($type){
            $map["contract_type"] = $type;
        }
        if(!empty($type)){
            $map["ca.status"] = ["in",$status];
        }
        $map["c.status"] = 1;

        //排序
        if($direction!="desc" && $direction!="asc"){
            $direction = "desc";
        }
        $order = $order." ".$direction;

        $contractList = $this->model->table($this->dbprefix."contract")->alias('c')
            ->join($this->table.' ca','ca.id = c.applied_id',"LEFT")
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->where($map)
            ->order($order)
            ->field('c.*,ca.contract_type,ca.contract_num,cs.contract_name as contract_type_name')
            ->select();
        return $contractList;
    }
    /**
     * 查询合同申请
     * @param $num int 数量
     * @param $page int 页
     * @param $filter array 合同筛选条件
     * @param $field array 合同列筛选条件
     * @param $order string 排序字段
     * @param $direction string 排序顺序
     * @return array|false
     * @throws \think\Exception
     */
    public function getContractApplied($num=10,$page=0,$filter=null,$field=null,$order="ca.id",$direction="desc"){
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

        $contractAppliedList = $this->model->table($this->table)->alias('ca')
            ->join($this->dbprefix.'contract_setting cs','cs.id = ca.contract_type',"LEFT")
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->field('ca.*,cs.contract_name as contract_type_name')
            ->select();
        if($num==1&&$page==0&&$contractAppliedList){
            $contractAppliedList = $contractAppliedList[0];
        }
        return $contractAppliedList;
    }
    /**
     * 查询合同数量
     * @param $filter array 合同筛选条件
     * @return array|false
     * @throws \think\Exception
     */
    public function getContractAppliedCount($filter=null){
        //筛选
        $map = $this->_getMapByFilter($filter,[]);

        $contractAppliedCount= $this->model
            ->table($this->table)
            ->where($map)
            ->count();
        return $contractAppliedCount;
    }

    protected function _getMapByFilter($filter,$filter_column){
        $map = [];
        return $map;
    }

    public function addContract($data){
        return $this->model->table($this->table)->insertGetId($data);
    }

    public function addAllContract($datas){
        $field = [
            'employee_id',
            'contract_type',
            'contract_num',
            'contract_apply_1',
            'contract_apply_2',
            'contract_apply_3',
            'contract_apply_4',
            'contract_apply_5',
            'contract_apply_6',
            'contract_apply_status',
            'update_time',
            'create_time',
            'status',
        ];
        return $this->model->table($this->table)->field($field)->insertAll($datas);
    }

    public function createContractNo($data){
        return $this->model->table($this->dbprefix."contract")->insertGetId($data);
    }

    public function createContractNos($datas){
        $field = [
            'applied_id',
            'contract_no',
            'update_time',
            'create_time',
            'status',
        ];
        return $this->model->table($this->dbprefix."contract")->field($field)->insertAll($datas);
    }

    public function getContract($id){
        return $this->model->table($this->table)->where('id',$id)->find();
    }

    public function setContract($id,$data){
        return $this->model->table($this->table)->where('id',$id)->update($data);
    }

    public function retract($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 3;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    public function approved($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 1;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    public function rejected($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 2;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    public function invalid($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 6;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    public function received($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 5;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    public function remind($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 8;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    public function refunded($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 9;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }

    public function withdrawal($id,$user_id=null){
        if($user_id){
            $data["employee_id"] = $user_id;
        }
        $data["status"] = 7;
        $map["id"] = $id;
        $map["status"] = 0;
        return $this->model->table($this->table)->where($map)->update($data);
    }
}