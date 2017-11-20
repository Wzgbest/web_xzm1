<?php
// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
namespace app\knowledgebase\model;

use app\common\model\Base;
use think\Db;

class CorporationSharePicture extends Base{
    protected $dbprefix;
    public function __construct($corp_id =null){
        $this->dbprefix = config('database.prefix');
        $this->table=config('database.prefix').'corporation_share_picture';
        parent::__construct($corp_id);
    }

    /**
     * 创建配图,并返回结果
     * @param $pictures array 配图信息数组
     * @return array
     * @throws \think\Exception
     */
    public function createMutipleCorporationSharePicture($pictures){
        $b = $this->model->table($this->table)->insertAll($pictures);
        return ['res'=>$b ,'error'=>"0"];
    }
}