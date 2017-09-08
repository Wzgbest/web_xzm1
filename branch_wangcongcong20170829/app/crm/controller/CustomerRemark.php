<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\crm\controller;
use app\common\controller\Initialize;
use app\common\model\ParamRemark;
class CustomerRemark extends Initialize{
    public function edit(){
        $id=request()->param('id');
        $title=request()->param('title');
        $redata['success']=false;
        $redata['msg']='保存失败';

        $paramModel=new ParamRemark($this->corp_id);
        if($id){
            //编辑
            $data['title']=$title;
            $re=$paramModel->setParam($id,$data);
            if($re!==false){
                $redata['success']=true;
                $redata['msg']='保存成功';
            }
        }
        else{
            //新增
            $userinfo = get_userinfo();
            $uid= $userinfo["userid"];
            $data['title']=$title;
            $data['add_man']=$uid;
            $re=$paramModel->addparamGetId($data);
            if($re)
            {
                $redata['success']=true;
                $redata['msg']='保存成功';
                $redata['num']=$re;
            }
        }
        return json($redata);
    }
    public function delete(){
        $id=request()->param('id');
        $redata['success']=false;
        $redata['msg']='删除失败';
        $paramModel=new ParamRemark($this->corp_id);
        if($id)
        {
            $re=$paramModel->delParam($id);
            if($re){
                $redata['success']=true;
                $redata['msg']='删除成功';
            }
        }
        return json($redata);
    }

}