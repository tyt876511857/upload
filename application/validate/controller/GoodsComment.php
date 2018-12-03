<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 15:21
 */

namespace app\validate\controller;

use think\Validate;

class GoodsComment extends Validate
{
    protected $rule = [
        'check_level'=>'require|between:0,2'
    ];

    protected $message = [
        'check_level.require' => '状态不能为空',
    ];

    protected $scene = [
        'edit' => ['check_level'],
    ];
}