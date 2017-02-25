<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Db;
use app\huanxin\model\Occupation;
use app\huanxin\model\CorporationStructure;

// 应用公共文件

//Db::listen( function ($sql, $time, $explain) {
//    echo $sql.' [execute time '.$time.'s]';
////    dump($explain);
//});

/**
 * 验证用户名格式
 * @param $tel
 * @return int
 */
function check_tel ($tel) {
    return preg_match('/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}/',$tel);
}

/**
 * 整合职位id和职位名称
 * @param $friendsInfo 用户信息二维数组
 * @param $corp_id 公司代号
 * @return array
 */
function get_occupation_name ($friendsInfo,$corp_id) {
    $occuM = new Occupation($corp_id);
    $occus = $occuM->getAllOccupations();
    foreach ($occus as $key => $val) {
        foreach ($friendsInfo as $k => $v) {
            if ($v['occupation'] == $val['id']) {
                $friendsInfo[$k]['occupation'] =$val['occu_name'];
            }
        }
    }
    return $friendsInfo;
}

/**
 * 整合部门id合部门名称
 * @param $friendsInfo
 * @param $corp_id
 * @return mixed
 */
function get_struct_name ($friendsInfo,$corp_id) {
    $structM = new CorporationStructure($corp_id);
    $structs = $structM->getAllStructure();
    foreach ($structs as $key => $val) {
        foreach ($friendsInfo as $k => $v) {
            if ($v['structid'] == $val['id']) {
                $friendsInfo[$k]['structid'] =$val['struct_name'];
            }
        }
    }
    return $friendsInfo;
}

/**
 * 权限验证
 * @param $rule 权限名称 string
 * @param $uid 用户id
 * @return bool
 */
function check_auth ($rule,$uid) {
    $corp_id = session('corp_id');$corp_id ='sdzhongxun';//TODO,更改
    $auth = new \myvendor\Auth($corp_id);
    if (!$auth->check($rule,$uid)) {
        return false;
    } else {
        return true;
    }
}