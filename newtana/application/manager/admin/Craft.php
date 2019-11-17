<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\Craft as CraftModel;
use app\common\model\CraftType as CraftTypeModel;

class Craft extends Admin
{

    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {


        $data_list = (new CraftModel())->where($this->getMap())
            ->alias('a')
            ->field('a.*,b.title as b_title')
            ->leftJoin('craft_type b','a.types_id = b.id')
            ->order('a.id', 'desc')
            ->paginate();

        $types = CraftTypeModel::where('status',1)->column('title','id');

        $autoAddItems = [
            ['select', 'types_id', '分类', '', $types],
            ['image', 'cover', '封面'],
            ['text', 'title', '标题'],
            ['number', 'sort', '排序', '', 100],
            ['ueditor', 'details', '详情图片'],
        ];

        $autoEditItems = [
            ['hidden','id'],
            ['select', 'types_id', '分类', '', $types],
            ['image', 'cover', '封面'],
            ['number', 'sort', '排序', '', 100],
            ['text', 'title', '标题'],
            ['ueditor', 'details', '详情图片'],
        ];


        return ZBuilder::make('table')
            ->setTableName('craft', true)
            ->autoAdd($autoAddItems, 'craft', '', true)
            ->addTopButtons(['enable', 'disable', 'delete'])
            ->addColumns([
                ['id', 'ID', 'text'],
                ['cover', '封面', 'picture'],
                ['b_title', '分类名称', 'text'],
                ['title', '标题', 'text'],
                ['create_time', '添加时间', 'datetime'],
                ['right_button', '操作', 'btn'],
            ])
            ->autoEdit($autoEditItems, 'craft', '', true)
            ->addRightButtons(['delete'])
            ->setRowList($data_list)
            ->fetch();
    }
}