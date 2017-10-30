<?php
namespace app\common\model;

use app\common\model\Base;

class Meme extends Base{
    protected $dbprefix;
    public function __construct($corp_id=null){
        $this->dbprefix = config('database.prefix');
        $this->table = $this->dbprefix.'meme';
        parent::__construct($corp_id);
    }

    /**
     * @param $data
     * @return int|string
     * 添加单个表情
     */
    public function addSingleMeme($data){
        return $this->model->table($this->table)->insertGetId($data);

    }

    /**
     * @param $data
     * @return int|string
     * 添加多个表情
     */
    public function addMutipleMeme($data){
        return $this->model->table($this->table)->insertAll($data);

    }

}