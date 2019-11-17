<?php
/**
 * Created by PhpStorm.
 * User: xiaomin
 * Date: 2019/4/21
 * Time: 12:30
 */

namespace app\common\validate;


use think\Validate;

class Carousel extends Validate
{

    protected $rule = [
        'cover' => ['require'],
    ];



}