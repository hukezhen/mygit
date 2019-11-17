<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\model;


use think\Model;

class Orders extends Model
{

    public $autoWriteTimestamp = true;


    public function detail()
    {
        return $this->hasMany('OrdersDetail', 'orders_id','id')
            ->alias('a')
            ->field([
                'a.*',
                'b.cover',
                'b.part_number',
                'b.diameter',
                'b.width',
                'b.et',
                'b.olt_hole',
                'b.pcd',
                'b.center_hole',
                'b.price',
                'group_concat(c.title) as molding_process_text',
                'group_concat(d.title) as surface_state_text',
                'e.title as series_text',
                'e.title_en as series_en_text',
                'f.title as brand_text',
                'g.title as vehicle_type_text',
                'h.title as motorcycle_type_text',
            ])
            ->leftJoin('product b','b.id = a.pid')
            ->leftJoin('product_type c', 'find_in_set(c.id,b.molding_process)')
            ->leftJoin('product_type d', 'find_in_set(d.id,b.surface_state)')
            ->leftJoin('product_type e', 'e.id = b.series')
            ->leftJoin('product_type f', 'f.id = b.brand')
            ->leftJoin('product_type g', 'g.id = b.vehicle_type')
            ->leftJoin('product_type h', 'h.id = b.motorcycle_type ')
            ->group('b.id');
    }

    public function manager()
    {
        return $this->hasMany('OrdersDetail', 'orders_id','id')
            ->alias('a')
            ->field([
                'a.*',
                'b.cover',
                'b.part_number',
                'b.diameter',
                'b.width',
                'b.et',
                'b.olt_hole',
                'b.pcd',
                'b.center_hole',
                'b.price',
            ])
            ->leftJoin('product b','b.id = a.pid');
    }

}