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

class Picture extends Base{
    public function __construct($corp_id){
        $this->table=config('database.prefix').'picture';
        parent::__construct($corp_id);
    }

    /**
     * 上传文件
     * @return array 文件信息数组
     */
    public function upload($type){
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
            $path = ROOT_PATH . 'public' . DS . 'webroot' . DS . $this->corp_id . DS . 'import_file';
            $checkFlg = $file->check(["ext"=>config('upload_image.image_ext')]);
            if(!$checkFlg){
                return false;
            }
            $info = $file->move($path);
            //var_exp($info,'$info');
            $savename = $info->getSaveName();
            $value['path'] = $savename;
            $value['category_id'] = $type;
            $value['md5'] = $info->hash("md5");
            $value['sha1'] = $info->hash("sha1");
            $value['create_time'] = time();
            if(isset($fileData[$value['md5']]) && is_numeric($fileData[$value['md5']])){
                $save_flg = $this
                    ->model
                    ->table($this->table)
                    ->where(array('id'=>$fileData[$value['md5']]))
                    ->update($value);
                $value['id'] = $fileData[$value['md5']];
                cache($this->corp_id."_img.".$value['id'],null);
            }else{
                $add_flg = $this
                    ->model
                    ->table($this->table)
                    ->insertGetId($value);
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
     * @param $type string hash类型
     */
    protected function getHash($file,$type='md5'){
        $hash_md5 = $file->hash($type);
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
        if(!$searchCustomerList){
            return false;
        }
        if($num==1&&$page==0){
            $searchCustomerList = $searchCustomerList[0];
        }
        return $searchCustomerList;
    }
}