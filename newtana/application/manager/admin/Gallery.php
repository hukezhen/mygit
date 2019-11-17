<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\Gallery as GalleryModel;
use app\common\model\Product as ProductModel;

class Gallery extends Admin
{


    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {

        $data_list = (new GalleryModel())->where($this->getMap())
            ->alias('a')
            ->field('a.*,b.title as b_title,c.title as c_title')
            ->leftJoin('gallery_type b','a.types_id = b.id')
            ->leftJoin('gallery_type c','b.pid = c.id')
            ->order('a.id', 'desc')
            ->paginate();



        return ZBuilder::make('table')
            ->setTableName('gallery', true)
            ->addTopButtons(['add','enable', 'disable', 'delete'])
            ->addColumns([
                ['id', 'ID', 'text'],
                ['c_title', '品牌', 'text'],
                ['b_title', '型号', 'text'],
                ['cover', '图片', 'pictures'],
                ['right_button', '操作', 'btn'],
            ])
            ->addRightButtons(['edit','delete'])
            ->setRowList($data_list)
            ->fetch();
    }

    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            // 验证
//            $result = $this->validate($data, 'Product');
//            if (true !== $result) {
//                $this->error($result);
//            }
            $data['pid'] = implode(',', $data['pid']);
            if ($link = GalleryModel::create($data)) {
                // 记录行为
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
        
        
        
        $product = ProductModel::where('status',1)->column('part_number','id');


        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['linkages', 'types_id', '品牌型号', '', 'gallery_type', 2, '', 'id,title,pid'],
                ['images', 'cover', '图片'],
                ['select', 'pid', '关联产品','',$product,'','multiple'],
            ])
            ->fetch();
    }
    public function edit($id = '')
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            // 验证
//            $result = $this->validate($data, 'Product');
//            if (true !== $result) {
//                $this->error($result);
//            }
            $data['pid'] = implode(',', $data['pid']);
            if ($link = GalleryModel::update($data)) {
                // 记录行为
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }

        $product = ProductModel::where('status',1)->column('part_number','id');
        $info = GalleryModel::get($id);

        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['hidden', 'id'],
                ['linkages', 'types_id', '品牌型号', '', 'gallery_type', 2, '', 'id,title,pid'],
                ['images', 'cover', '图片'],
                ['select', 'pid', '关联产品','',$product,'','multiple'],
            ])
            ->setFormData($info)
            ->fetch();
    }



}