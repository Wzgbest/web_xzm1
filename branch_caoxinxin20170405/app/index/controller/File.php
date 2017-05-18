<?php
namespace app\index\controller;

use think\Controller;
use app\common\model\File as FileModel;

class File extends Controller{
    protected $_fileModel = null;
    public function __construct(){
        parent::__construct();
        //session('userinfo.corp_id','sdzhongxun');
        $corp_id = get_corpid();
        $this->_fileModel = new FileModel($corp_id);
    }

    public function upload(){
        $result  = ['status' => 1, 'info' => '上传成功!'];
        $infos = $this->_fileModel->upload();
        if($infos){
            $result['data'] = $infos;
        } else {
            $result['status'] = 0;
            $result['info'] = '上传失败!';
        }
        return json_encode($result);
    }
}
