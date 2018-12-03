<?php

namespace app\validate\controller;

use think\Validate;

class Task extends Validate
{
    protected $rule = [
        'run_way' => 'require|between:0,8',
        'attache' => 'require|chs',
        'operator' => 'require|chs',
        'art_designer'=> 'require|chs',
        'pj_why'=> 'require|chs',
        'group' => 'require|between:0,8',
        'key_num' => 'require|between:1,10',
        'count' => 'require|between:1,1000',
        'level' => 'require|between:1,127',
        'category' => 'require',
        'is_group' => 'require',
        'is_virtual' => 'require',
        'pj_coupons' => 'require',
        'is_xs' => 'require',
        'baby_id' => 'require',
        'is_dzyh' => 'require',
        'detail' => 'require',
        'shop'=>'require',
        'hint'=>'require',
        'baby_goods'=>'require',
        'goods_price'=>'require|float',
        'coupon_money'=>'require|float',
        'new_pass'=>'require',
        'true_pay'=>'require',
        'img_link'=>'require'
    ];

    protected $message = [
        'attache.require' => '缺少运营员',
        'operator.require' => '操作员不能为空',
        'art_designer.require' => '美工不能为空',
        'conf_password' => '确认密码错误',
        'hint.require' => '提示不能为空',
        'img_link.require' => '图片链接不能为空',
        'run_way.require' => '运营方式不能为空',
    ];

    protected $scene = [
        'add' => ['hint','img_link','baby_goods','goods_price','shop','run_way', 'attache', 'operator','art_designer', 'pj_why', 'group', 'key_num', 'level', 'category','baby_id','detail'],
        'edit' => ['name', 'job', 'wechat','sex','birthday'],
    ];

}