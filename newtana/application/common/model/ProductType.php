<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\model;


use think\Model;

class ProductType extends Model
{

    public $autoWriteTimestamp = true;

    /**
     * @param $value
     * @return mixed|string
     */
    public function getTypeAttr($value)
    {
        return self::getTypeData()[$value] ?? '';
    }

    /**
     * @return array
     */
    public static function getTypeData(){
        return [
            1 => '所属系列',
            2 => '品牌',
            3 => '车辆类型',
            4 => '车型',
            5 => '表面状态',
            6 => '成型工艺'
        ];
    }

}