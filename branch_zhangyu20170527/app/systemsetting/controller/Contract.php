<?php
/**
 * Created by messhair.
 * Date: 2017/5/25
 */
namespace app\systemsetting\controller;

use app\common\controller\Initialize;
use app\systemsetting\model\Contract as ContractModel;
use think\Request;

class Contract extends Initialize
{
    public function index()
    {
        $conM = new ContractModel();
        $res = $conM->getAllContract();
        $this->assign('contracts',$res);
        return view();
    }

    public function editContract(Request $request)
    {
        $input = $request->param();
        $conM = new ContractModel();
        if ($request->isGet()) {
            $res = $conM->getContractById($input['corp_id']);
            return $res;
        } elseif ($request->isPost()) {
//            $data = $conM->get
        }
    }
}