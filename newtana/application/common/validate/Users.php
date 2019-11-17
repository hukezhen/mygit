<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\validate;


use think\Validate;

class Users extends Validate
{

    protected $rule = [
        'username|用户名' => ['require', 'unique' => 'users,username'],
        'password|密码' => ['require', 'length' => '6,16'],
        'nickname|联系人' => ['require'],
//        'company|公司名称' => ['require'],
        'telephone|手机' => ['require','unique' => 'users,telephone'],
//        'phone|座机' => ['require'],
        'email|邮箱' => ['require'],
//        'address|地址' => ['require'],
        'type|用户类型' => ['require'],
    ];

}