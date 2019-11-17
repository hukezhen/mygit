<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\GalleryType as GalleryTypeModel;

class GalleryType extends Admin
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
        $data_list = (new GalleryTypeModel())->where($this->getMap())
            ->order('id', 'desc')
            ->paginate();


        $pid = GalleryTypeModel::where('pid',0)->column('title','id');
        $temp[0] = '品牌';
        foreach ($pid as $k=>$v){
            $temp[$k] = $v;
        }
        $autoAddItems = [
            ['select', 'pid', '品牌', '', $temp],
            ['text', 'title', '字段名称'],
        ];
        $autoEditItems = [
            ['hidden','id'],
            ['select', 'pid', '品牌', '', $temp],
            ['text', 'title', '字段名称'],
        ];

        return ZBuilder::make('table')
            ->setTableName('gallery_type', true)
            ->autoAdd($autoAddItems,'gallery_type','',true)
            ->addTopButtons(['enable', 'disable', 'delete'])// 添加顶部按钮
            ->setSearch(['title' => '字段名称'])
            ->addColumns([
                ['id', 'ID', 'text'],
                ['pid', '类型', 'callback',function($value){
                    return $value == 0 ? '品牌' : '型号';
                }],
                ['title', '名称', 'text'],
                ['create_time', '添加时间', 'datetime'],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn'],
            ])
            ->setColumnWidth('id', 50)
            ->autoEdit($autoEditItems,'gallery_type','',true)
            ->addRightButtons([ 'delete'])
            ->setRowList($data_list)
            ->fetch();
    }
}