<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 生成应用公共文件
    '__file__' => ['command.php', 'common.php', 'config.php', 'database.php', 'route.php', 'tags.php'],
    //API       模块
    'api'     => [
        '__file__'   => ['common.php','config.php'],
        '__dir__'    => ['controller'],
        'controller' => ['Base','Area'],
    ],
    //公共       模块
    'common'     => [
        '__file__'   => ['common.php','config.php'],
        '__dir__'    => ['model','validate','behavior','controller','view'],
        'model'      => ['Area','Explain'],
        'validate'   => ['Area','Explain'],
        'behavior'   => [],
        'controller' => ['Base'],
        'view'       => [
            'Tpl-404',
        ],
    ],
    //Index     模块
    'index'     => [
        '__file__'   => ['common.php','config.php'],
        '__dir__'    => ['controller','view'],
        'controller' => ['Base'],
        'view'       => [
            'Index-index',
        ],
    ],
    //后台       模块
    'admin'     => [
        '__file__'   => ['common.php','config.php'],
        '__dir__'    => ['controller'],
        'controller' => ['Base','index','Fm'],
        'view'       => [

        ],
    ],
    // 测试       模块
    'testing'   =>[
        '__file__'   => ['common.php','config.php'],
        '__dir__'    => ['controller','model'],
        'controller'=>['Base'],
        'model'=>[],

    ],
];
