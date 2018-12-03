<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 15:21
 */

namespace app\validate\controller;

use think\Validate;

class Setting extends Validate
{
    protected $rule = [
        'MAX_COM_NUM'=>'require|integer',
        'MAX_DAY_NUM'=>'require|integer',
        'MAX_DAY_TASK_NUM'=>'require|float|integer',
        'TaskInterval'=>'require|integer',
        'MAX_DAY_REFUND_NUM'=>'require|integer',
        'QUEUE_EXPIRE'=>'require|integer',
        'WECHAT_WEB_URL'=>'require'
    ];

    protected $message = [

    ];

    protected $scene = [
        'edit' => ['MAX_COM_NUM','MAX_DAY_NUM','MAX_DAY_TASK_NUM','TaskInterval','MAX_DAY_REFUND_NUM','QUEUE_EXPIRE','WECHAT_WEB_URL'],
    ];
}