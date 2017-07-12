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

class ImportFile extends Base{
    public function __construct($corp_id){
        $this->table=config('database.prefix').'import_file';
        parent::__construct($corp_id);
    }

    /**
     * 上传文件
     * @return array 文件信息数组
     */
    public function upload($type){
        $files = request()->file('files');
        if(empty($files)){
            return false;
        }
        $infos = [];
        $files_hash = [];
        if(is_array($files)){
            $files_hash = array_map([__CLASS__,'getHash'],$files);
        }else{
            $files_hash[] = $this->getHash($files);
        }
        $fileHashData = $this
            ->model
            ->table($this->table)
            ->where(["md5"=>["in",$files_hash]])
            ->column('md5,id');

        if(is_array($files)) {
            foreach ($files as $key => $file) {
                $value = $this->getUploadFileInfo($file,$type,$fileHashData);
                $infos[] = $value;
            }
        }else{
            $value = $this->getUploadFileInfo($files,$type,$fileHashData);
            $infos[] = $value;
        }
        if($infos){
            return $infos;
        }else{
            return false;
        }
    }
    /**
     * 处理文件
     * @param $file Object File类对象
     * @param $type int 文件分类
     * @param $fileHashData array 已上传文件hash
     * @return array 文件信息
     */
    protected function getUploadFileInfo($file,$type,$fileHashData){
        $value = [];
        $path = ROOT_PATH . 'public' . DS . 'webroot' . DS . $this->corp_id . DS . 'import_file';
        $upload_import_file_rule = config('upload_import_file');
        $rule["type"] = $upload_import_file_rule["type"];
        $rule["ext"] = $upload_import_file_rule["ext"];
        $checkFlg = $file->check($rule);
        if (!$checkFlg) {
            return false;
        }
        $info = $file->move($path);
        //var_exp($info,'$info');
        $savename = $info->getSaveName();
        $original_info = $info->getInfo();
        $value['type'] = $type;
        $value['name'] = $original_info['name'];
        $value['savename'] = basename($savename);
        $value['savepath'] = $path . DS . dirname($savename);
        $value['ext'] = pathinfo($value['name'], PATHINFO_EXTENSION);
        $value['mime'] = $info->getMime();
        $value['size'] = $info->getSize();
        $value['md5'] = $info->hash("md5");
        $value['sha1'] = $info->hash("sha1");
        $value['location'] = 0;
        $value['create_time'] = time();
        if (isset($fileHashData[$value['md5']]) && is_numeric($fileHashData[$value['md5']])) {
            $save_flg = $this
                ->model
                ->table($this->table)
                ->where(array('id' => $fileHashData[$value['md5']]))
                ->update($value);
            $value['id'] = $fileHashData[$value['md5']];
        } else {
            $add_flg = $this
                ->model
                ->table($this->table)
                ->insertGetId($value);
            if ($add_flg) {
                $value['id'] = $add_flg;
            }
        }
        return $value;
    }
    /**
     * 获取文件
     * @param $file Object File类对象
     * @param $type string hash类型
     * @return string md5字符串
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
