<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\validate;


use think\Validate;

class Product extends Validate
{

    protected $rule = [
        'title|轮毂名称' => 'require',
        'series|系列' => 'require',
        'brand|品牌' => 'require',
        'vehicle_type|车辆类型' => 'require',
        'motorcycle_type|车型' => 'require',
        'part_number|零件号' => ['require','unique'=>'product,part_number'],
        'surface_state|表面状态' => 'require',
        'molding_process|成型工艺' => 'require',
        'price|价格' => ['require','gt:0']
    ];

    protected $message = [
        'title.require' => '轮毂名称不能为空',
        'series.require' => '系列不能为空',
        'brand.require' => '品牌不能为空',
        'vehicle_type.require' => '车辆类型不能为空',
        'motorcycle_type.require' => '车型不能为空',
        'part_number.require' => '零件号不能为空',
        'surface_state.require' => '表面状态不能为空',
        'molding_process.require' => '成型工艺不能为空',
        'price.require' => '价格不能为空'
    ];


}