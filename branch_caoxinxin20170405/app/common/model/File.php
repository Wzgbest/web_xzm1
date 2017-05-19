<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\common\model;

use app\common\model\Base;

class File extends Base{
    public function __construct($corp_id){
        $this->table=config('database.prefix').'file';
        parent::__construct($corp_id);
    }

    /**
     * 上传文件
     * @return array 文件信息数组
     */
    public function upload(){
        $files = request()->file('files');
        if(!$files){
            return false;
        }
        $infos = [];
        $files_hash = array_map([__CLASS__,'getHash'],$files);
        $fileData = $this
            ->model
            ->table($this->table)
            ->where(["md5"=>["in",$files_hash]])
            ->column('md5,id');
        foreach($files as $key=>$file){
            $value = [];
            $path = ROOT_PATH . 'public' . DS . 'uploads';
            $info = $file->move($path);
            //var_exp($info,'$info');
            $savename = $info->getSaveName();
            $original_info = $info->getInfo();
            $value['name'] = $original_info['name'];
            $value['savename'] = basename($savename);
            $value['savepath'] = $path.DS.dirname($savename);
            $value['ext'] = pathinfo($value['name'], PATHINFO_EXTENSION);
            $value['mime'] = $info->getMime();
            $value['size'] = $info->getSize();
            $value['md5'] = $info->hash("md5");
            $value['sha1'] = $info->hash("sha1");
            $value['location'] = 0;
            $value['create_time'] = time();
            if(isset($fileData[$value['md5']]) && is_numeric($fileData[$value['md5']])){
                $save_flg = $this
                    ->model
                    ->table($this->table)
                    ->where(array('id'=>$fileData[$value['md5']]))
                    ->update($value);
                $value['id'] = $fileData[$value['md5']];
            }else{
                $add_flg = $this
                    ->model
                    ->table($this->table)
                    ->insert($value);
                if($add_flg){
                    $value['id'] = $add_flg;
                }
            }
            $infos[] = $value;
        }
        if($infos){
            return $infos;
        }else{
            return false;
        }
    }
    /**
     * 获取文件
     * @param $file Object File类对象
     * @return string md5字符串
     */
    protected function getHash($file){
        $hash_md5 = $file->hash('md5');
        return $hash_md5;
    }

    /**
     * 获取文件
     * @param $num int 数量
     * @param $page int 页
     * @param $map array 筛选条件
     * @param $order string 排序
     * @return array
     * @throws \think\Exception
     */
    public function get($num=10,$page=0,$map=null,$order="id desc"){
        $offset = 0;
        if($page){
            $offset = $page*$num;
        }
        $searchCustomerList = $this->model
            ->table($this->table)
            ->where($map)
            ->order($order)
            ->limit($offset,$num)
            ->select();
        return ['res'=>$searchCustomerList ,'error'=>"0"];
    }
}