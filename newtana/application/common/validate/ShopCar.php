<?php
/**
 *Created by PhpStorm.
 *User:she.
 *Date:2019/9/14.
 */

namespace app\common\validate;


use think\Validate;

class ShopCar extends Validate
{
    protected $rule = [
        'uid|UID' => 'require',
        'goods_id|商品ID' => 'require',
    ];
}

