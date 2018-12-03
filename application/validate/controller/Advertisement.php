<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 15:21
 */

namespace app\validate\controller;

use think\Validate;

class Advertisement extends Validate
{
    protected $rule = [
        'content'=>'require|max:200',
        'is_hot'=>'require|between:0,1',
        'is_new'=>'require|between:0,1'
    ];

    protected $message = [
        'content.require' => '内容不能为空',
        'is_hot.require' => '热门不能为空',
        'is_new.require' => '是否最新不能为空',
    ];

    protected $scene = [
        'add' => ['content','is_hot','is_new'],
        'edit' => ['content','is_hot','is_new'],
    ];
}