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

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------
use think\facade\Env;

return [
    // 驱动方式
    'type'   => 'redis',
    // 缓存保存目录
    'path'   => '',
    // 缓存前缀
    'prefix' => '',
    // 缓存有效期 0表示永久缓存
    'expire' => 0,
    //redis配置
    'host'=>Env::get('redis.host'),
    'port'=>Env::get('redis.port'),
    'select'=>Env::get('redis.db'),
    'password'=>Env::get('redis.password')
];