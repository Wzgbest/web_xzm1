<?php
/**
 * Created by messhair.
 * Date: 2017/1/13
 */
namespace Huanxin\Controller;

use Huanxin\Model\UserCorporationModel;
use Think\Controller;
use Huanxin\Model\EmployerModel;
use Think\Model;

class NewVerifyUserController extends Controller{

    public function index() {
//        $input=I('post.');
        $input=I('get.');
        $telephone=trim($input['telephone']);
        $password=trim($input['password']);
        $req_reg['status']=false;
        if($telephone==''||$password==''){
            $req_reg['message']='ȱ�ٱ�����Ϣ';
        }else{
            $corp=new UserCorporationModel();
            $corp_id=$corp->getUserCorp($telephone);
            if(empty($corp_id)){
                $req_reg['message']='�û������ڻ��û�δ���ֹ�˾����';
            }else{
//                $newM=$this->dbChange(C('DB_COMMON_PREFIX').$corp_id); //��Ӧ$this->dbChange()����
                $model=new EmployerModel(C('DB_COMMON_PREFIX').$corp_id.'.'.'employer');
                $user_arr=$model->getEmployer($telephone);
                if(empty($user_arr)){
                    $req_reg['message']='�û������ڻ��û�δ���ֹ�˾����';
                }else{
                    if($user_arr['password'] != md5($password)){
                        $req_reg['message']='�������';
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

    /**
     * ������ʱ����
     * @param $db
     * @return Model
     */
    protected function dbChange($db) {
        //mysqli://root:1234@localhost:3306/thinkphp#utf8
        $conn=C('DB_TYPE').'://'.C('DB_USER').':'.C('DB_PWD').'@'.C('DB_HOST').':'.C('DB_PORT').'/'.$db.'#utf8';
//        $model=new Model('corp',C('DB_PREFIX'),$conn);
        $newM=new Model();
        $newM->db(1,$conn);
        return $newM;
    }
}
