<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\CraftType as CraftTypeModel;

class CraftType extends Admin
{
    /**
     * 首页.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function index()
    {
        $data_list = (new CraftTypeModel())->where($this->getMap())
            ->order('id', 'desc')
            ->paginate();
        $autoAddItems = [
            ['text', 'title', '分类名称'],
            ['text', 'title_en', '分类名称(英文)'],
            ['number', 'sort', '排序', '', 100],
        ];
        $autoEditItems = [
            ['hidden', 'id'],
            ['text', 'title', '分类名称'],
            ['text', 'title_en', '分类名称(英文)'],
            ['number', 'sort', '排序', '', 100],
        ];

        return ZBuilder::make('table')
            ->setTableName('craft_type', true)
            ->autoAdd($autoAddItems, 'craft_type', '', true)
            ->addTopButtons(['enable', 'disable', 'delete'])// 添加顶部按钮
            ->setSearch(['title' => '分类名称'])
            ->addColumns([
                ['id', 'ID', 'text'],
                ['title', '分类名称', 'text'],
                ['title_en', '分类名称(英文)', 'text'],
                ['create_time', '添加时间', 'datetime'],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn'],
            ])
            ->setColumnWidth('id', 50)
            ->autoEdit($autoEditItems, 'craft_type', '', true)
            ->addRightButtons(['delete'])
            ->setRowList($data_list)
            ->fetch();
    }
}