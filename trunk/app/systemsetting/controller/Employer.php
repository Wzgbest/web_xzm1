<?php
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Employer as EmployerModel;

class Employer extends Initialize
{
    public function index()
    {}

    /**
     * 查看员工详情
     * @param $user_id 员工id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function showSingleEmployerInfo($user_id)
    {
        $corp_id = get_corpid();
        $employerM = new EmployerModel($corp_id);
        $info = $employerM->getEmployerByUserid($user_id);
        return $info;
    }

    /**
     * 分页展示员工列表
     * @param int $page_now_num 当前页
     * @param int $page_rows 行数
     * @return array
     */
    public function showEmployerList($page_now_num = 0, $page_rows = 10,$where = null)
    {
        $corp_id = get_corpid();
        $employerM = new EmployerModel($corp_id);
        $map = [
            'struct_id' =>$where['struct_id'],
            'role' => $where['role'],
            'on_duty' =>$where['on_duty'],
        ];
        $res = $employerM->getPageEmployerList($page_now_num,null,$map);dump($res);exit;
        $count = $employerM->countPageEmployerList($map);
        return [
            'data'=>$res,
            'page_now_num'=>$page_now_num,
            'page_row'=>$page_rows,
            'total_num'=>$count,
        ];
    }
}