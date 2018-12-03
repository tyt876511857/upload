<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 15:21
 */

namespace app\validate\controller;

use think\Validate;

class Accusation extends Validate
{
    protected $rule = [
        'img'=>'require|max:200',
        'type'=>'between:0,1',
        'order'=>'require|max:30',
        'status'=>'require|between:0,1'
    ];

    protected $message = [
        'img.require' => '图片不能为空',
        'type.require' => '类型不能为空',
        'status.require' => '状态不能为空',
    ];

    protected $scene = [
        'add' => ['img','order'],
        'edit' => ['status'],
    ];
}