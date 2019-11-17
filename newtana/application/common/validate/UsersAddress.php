<?php
/**
 *Created by PhpStorm.
 *User:she.
 *Date:2019/9/13.
 */

namespace app\common\validate;


use think\Validate;

class UsersAddress extends Validate
{


    protected $rule = [
        'uid|UID' => 'require',
        'name|联系人' => 'require',
        'address|详细地址' => 'require',
        'postal_code|联系电话' => ['require','length' => 11, 'regex' => '/^1([3456789])\\d{9}$/'],
    ];

}

