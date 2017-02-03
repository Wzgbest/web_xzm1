<?php
/**
 * Created by messhair.
 * Date: 2017/1/13
 */
namespace Huanxin\Model;

use Think\Model;

class CorporationModel extends Model{

    /**
     * 根据公司代号查询
     * @param $corp_id
     * @return mixed
     */
    public function getCorporation($corp_id) {
        return $this->field('id,corp_id,corp_name')->where(array('corp_id'=>$corp_id))->find();
    }
}