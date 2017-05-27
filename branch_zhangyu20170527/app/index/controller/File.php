<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\common\controller\Initialize;
use app\common\model\File as FileModel;

class File extends Initialize{
    protected $_fileModel = null;
    public function __construct(){
        parent::__construct();
        $corp_id = get_corpid();
        $this->_fileModel = new FileModel($corp_id);
    }

    public function upload(){
        $result  = ['status' => 1, 'info' => '上传成功!'];
        $type = input("type");
        if(!$type){
            $result['status'] = 0;
            $result['info'] = '上传失败,参数有误!';
            return json_encode($result);
        }
        $infos = $this->_fileModel->upload($type);
        if($infos){
            $result['data'] = $infos;
        } else {
            $result['status'] = 0;
            $result['info'] = '上传失败!';
        }
        return json_encode($result);
    }
}
