<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// php think build --config build.php

return [
    // 生成应用公共文件
    '__file__' => ['common.php', 'config.php', 'database.php'],

    // 定义demo模块的自动生成 （按照实际定义的文件名生成）
//    'huanxin'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['behavior', 'controller', 'model', 'view'],
//        'controller' => ['Index', 'Login','Friend'],
//        'model'      => [],
//        'view'       => ['index/index'],
//    ],
//    'common'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['behavior', 'controller', 'model', 'view','service'],
//        'controller' => ['Index'],
//        'model'      => ['Umessage'],
//        'view'       => ['index/index'],
//    ],
//    'workerman'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['controller', 'model', 'view','service'],
//        'controller' => ['Index'],
//        'model'      => [],
//        'view'       => ['index/index'],
//    ],
    'crm'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'model', 'view','service','logic'],
        'controller' => ['Index','Customer','SearchCustomer','SaleChance','Mailer','ContractApply','BillApply','RingUp'],
        'model'      => [],
        'view'       => ['index/index'],
    ],
    'systemsetting'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'model', 'view','service'],
        'controller' => ['Index','Corporation','Department','Role','Employer','BusinessFlow','Contract','Bill','Customer'],
        'model'      => [],
        'view'       => ['index/index'],
    ],
    'verification'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'model', 'view','service'],
        'controller' => ['Index','Contract','Bill'],
        'model'      => [],
        'view'       => ['index/index'],
    ],
    'phonebox'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'model', 'view','service'],
        'controller' => ['Index','UploadFile','PositiveRequest','ConnectKeep'],
        'model'      => [],
        'view'       => ['index/index'],
    ],
    'knowledgebase'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'model', 'view','service'],
        'controller' => ['Index','TalkingMethod','CorporationShare','CloudFile','LiveShow'],
        'model'      => [],
        'view'       => ['index/index'],
    ],
    'employergrowth'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'model', 'view','service'],
        'controller' => ['Index','Bill'],
        'model'      => [],
        'view'       => ['index/index'],
    ],
];
