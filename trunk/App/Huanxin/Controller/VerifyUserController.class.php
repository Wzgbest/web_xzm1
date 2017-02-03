<?php
/**
 * Created by messhair.
 * Date: 2017/1/13
 */
namespace Huanxin\Controller;

use Huanxin\Model\UserCorporationModel;
use Think\Controller;
use Huanxin\Model\EmployerModel;

class VerifyUserController extends Controller{

    /**
     * 验证环信客户端用户登录验证请求
     * @param Array[ telephone password ]
     * @return json_string
     */
    public function index() {
        $input=I('post.');
//        file_put_contents('d:/hu.txt',json_encode($input,true),FILE_APPEND);
//        $input=I('get.');
        $telephone=trim($input['telephone']);
        $password=trim($input['password']);
        $req_reg['status']=false;
        if($telephone==''||$password==''){
            $req_reg['message']='缺少必填信息';
        }else{
            $corp=new UserCorporationModel();
            $corp_id=$corp->getUserCorp($telephone);
            if(empty($corp_id)){
                $req_reg['message']='用户不存在或用户未划分公司归属';
            }else{
//                $newM=$this->dbChange(C('DB_COMMON_PREFIX').$corp_id); //对应$this->dbChange()方法
                $model=new EmployerModel(C('DB_COMMON_PREFIX').$corp_id.'.'.'employer');
                $user_arr=$model->getEmployer($telephone);
                if(empty($user_arr)){
                    $req_reg['message']='用户不存在或用户未划分公司归属';
                }else{
                    if($user_arr['password'] != md5($password)){
                        $req_reg['message']='密码错误';
                    }else{
                        $req_reg['message']='SUCCESS';
                        $req_reg['status']=true;
                        $req_reg['nickname']=$user_arr['truename'];
                        $req_reg['userpic']=$user_arr['userpic'];
                    }
                }
            }
        }
        echo json_encode($req_reg,true);
    }
}
