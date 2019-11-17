<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\validate;


use think\Validate;

class ProductType extends Validate
{

    protected $rule = [
        'title' => ['require'],
        'type' => ['require'],
    ];

}