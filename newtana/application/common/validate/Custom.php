<?php
/**
 *Created by PhpStorm.
 *User:she.
 *Date:2019/9/15.
 */

namespace app\common\validate;


use think\Validate;

class Custom extends Validate
{

    protected $rule = [
        'title|标题' => 'require',
        'price|基础价格' => 'require',
        'state|表面状态' => 'require',
        'cover|封面图' => 'require',
    ];

}

