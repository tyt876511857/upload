<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 15:21
 */

namespace app\validate\controller;

use think\Validate;

class Recharge extends Validate
{
    protected $rule = [
        'recharge_man'=>'require',
        'money'=>'require|float',
        'order'=>'require'
    ];

    protected $message = [

    ];

    protected $scene = [
        'add' => ['recharge_man','money','order'],
    ];
}