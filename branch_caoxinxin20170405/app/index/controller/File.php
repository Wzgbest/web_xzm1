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
            /* Excel读取和保存演示
            var_exp($infos,'$infos');
            $column = array (
                'A' => 'kf_nick',
                'B' => 'kf_wx',
                'C' => 'kf_account',
                'D' => 'uid',
                'E' => 'no',
                'F' => 'password',
            );
            foreach ($infos as $info){
                $res = importFormExcel($info['id'],$column);
                var_exp($res['data'],'$res[\'data\']');
                //outExcel($res['data'],$info['name']);
                $save_flg = saveExcel($res['data']);
                $file_mark = "<a href='$save_flg' target='_blank'>$save_flg</a>";
                echo $file_mark;
                var_exp($save_flg,'$save_flg',1);
            }
            */
            $result['data'] = $infos;
        } else {
            $result['status'] = 0;
            $result['info'] = '上传失败!';
        }
        return json_encode($result);
    }
}
