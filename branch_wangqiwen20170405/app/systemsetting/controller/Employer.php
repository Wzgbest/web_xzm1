<?php
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\common\model\Employer as EmployerModel;
use app\huanxin\service\Api as HuanxinApi;
use app\common\model\StructureEmployer;

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
     * @param int $struct_id 部门id
     * @param int $role 角色id
     * @param int $on_duty 状态
     * @return array
     */
    public function showEmployerList($page_now_num = 0, $page_rows = 10,$map = null)
    {
        $corp_id = get_corpid();
        $input = input('param.');
        $employerM = new EmployerModel($corp_id);
        $map = [];
        if (isset($input['struct_id'])) {
            $map['struct_id'] = $input['struct_id'];
        }
        if (isset($input['role'])) {
            $map['role'] = $input['role'];
        }
        if (isset($input['on_duty'])) {
            $map['on_duty'] = $input['on_duty'];
        }
        $res = $employerM->getPageEmployerList($page_now_num,$page_rows,$map);
        $count = $employerM->countPageEmployerList($map);
        $count = empty($count)? 0:$count[0]['num'];
        return [
            'data'=>$res,
            'page_now_num'=>$page_now_num,
            'page_row'=>$page_rows,
            'total_num'=>$count,
        ];
    }

    public function addEmployer()
    {
        $input = input('param.');
        $result = $this->validate($input,'Employer');
        $info['status'] = false;
        if(true !== $result){
            $info['message'] = $result;
            return $info;
        }
        $corp_id = get_corpid();
        $employerM = new EmployerModel($corp_id);
        $struct_empM = new StructureEmployer($corp_id);
        $huanxin = new HuanxinApi();
        $info['status'] = false;
        $employerM->link->startTrans();
        try{
            $b = $employerM->addSingleEmployer($input);dump($b);
//            $d = $huanxin->addFriend($input['telephone']);
            $struct_empM->addStructureEmployer();

            $d['status'] = true;
            if ( $d['status'] ) {
                $tel = [];
                array_push($tel,$input['telephone']);
                $im = $employerM->saveIm($tel);dump($b);
            } else {
                $employerM->link->rollback();
                $info['message'] = '添加环信好友有失败，联系管理员';
                $info['error'] = $d['error'];
                return $info;
            }
        }catch (\Exception $e){
            $employerM->link->rollback();

        }
        dump($b);dump($im);exit;
        if ($b > 0 && $d['status'] && $im > 0) {
//            $employerM->link->commit();
            $employerM->link->rollback();
            return [
                'status' => true,
                'message' => '新增员工成功，添加环信好友成功',
            ];
        } else {
            $employerM->link->rollback();
            $info['message'] = '新增员工失败，或添加环信好友失败';
            return $info;
        }
    }
    public function editEmployer($user_id)
    {
        $input = input('param.');

    }
}