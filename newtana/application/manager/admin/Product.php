<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\Product as ProductModel;
use app\common\model\ProductType as ProductTypeModel;


class Product extends Admin
{

    /**
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {
        $data_list = (new ProductModel())->where($this->getMap())
            ->order('id', 'desc')
            ->where('mode',1)
            ->paginate();


        return ZBuilder::make('table')
            ->setTableName('product', true)
            ->addTopButtons(['add', 'enable', 'disable', 'delete'])// 添加顶部按钮
            ->setSearch(['title' => '轮毂名称'])
            ->addColumns([
                ['id', 'ID', 'text'],
                ['part_number', '产品型号', 'text'],
                ['title', '轮毂名称', 'text'],
                ['sort', '排序', 'text.edit'],
                ['create_time', '添加时间', 'datetime'],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn'],
            ])
            ->setColumnWidth('id', 50)
            ->addRightButtons(['edit', 'delete'])
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
            $result = $this->validate($data, 'Product');
            if (true !== $result) {
                $this->error($result);
            }
            $data['surface_state'] = implode(',', $data['surface_state']);
            $data['molding_process'] = implode(',', $data['molding_process']);
            if ($link = ProductModel::create($data)) {
                // 记录行为
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }

        //所属系列
        $series = ProductTypeModel::where('type', 1)->column('title', 'id');
        //品牌
        $brand = ProductTypeModel::where('type', 2)->column('title', 'id');
        //车辆类型
        $vehicleType = ProductTypeModel::where('type', 3)->column('title', 'id');
        //车型
        $motorcycleType = ProductTypeModel::where('type', 4)->column('title', 'id');
        //表面状态
        $surfaceState = ProductTypeModel::where('type', 5)->column('title', 'id');
        //成型工艺
        $moldingProcess = ProductTypeModel::where('type', 6)->column('title', 'id');


        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['text', 'title', '轮毂名称'],
                ['select:6', 'series', '所属系列', '', $series],
                ['select:6', 'brand', '品牌', '', $brand],
                ['select:6', 'vehicle_type', '车辆类型', '', $vehicleType],
                ['select:6', 'motorcycle_type', '车型', '', $motorcycleType],
                ['image', 'cover', '图片'],
                ['text:6', 'part_number', '零件号'],
                ['number:6', 'price', '价格'],
                ['text:6', 'diameter', '尺寸（直径）'],
                ['text:6', 'width', '尺寸（宽度 J）'],
                ['text:6', 'et', 'ET'],
                ['text:6', 'olt_hole', '螺栓孔数'],
                ['text:6', 'pcd', 'pcd'],
                ['text:6', 'center_hole', '中心孔'],
                ['text:12', 'tire_width', '推荐轮胎'],
                ['text:6', 'load', '最大载荷'],
                ['text:6', 'weight', '重量KG'],
                ['select:6', 'surface_state', '表面状态', '', $surfaceState, '', 'multiple'],
                ['select:6', 'molding_process', '成型工艺', '', $moldingProcess, '', 'multiple'],
                ['radio', 'recommend', '推荐产品', '', [0 => '不推荐', 1 => '最新产品', 2 => '热销爆款'], 0],
                ['image', 'recommend_img', '新品/爆款封面'],
                ['text', 'video_url', '视频链接'],
                ['number:6', 'sort', '排序', '', 100],
                ['number:6', 'stock', '库存', '', 100],
                ['ueditor', 'details', '详情图片'],

            ])
            ->setTrigger('recommend', '1,2', 'recommend_img')
            ->fetch();
    }


    /**
     * 编辑.
     *
     * @param string $id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function edit($id = '')
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            $data['surface_state'] = implode(',', $data['surface_state']);
            $data['molding_process'] = implode(',', $data['molding_process']);
            // 验证
            $result = $this->validate($data, 'Product');
            if (true !== $result) {
                $this->error($result);
            }
            if ($link = ProductModel::update($data)) {
                // 记录行为
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        $info = ProductModel::get($id);


        //所属系列
        $series = ProductTypeModel::where('type', 1)->column('title', 'id');
        //品牌
        $brand = ProductTypeModel::where('type', 2)->column('title', 'id');
        //车辆类型
        $vehicleType = ProductTypeModel::where('type', 3)->column('title', 'id');
        //车型
        $motorcycleType = ProductTypeModel::where('type', 4)->column('title', 'id');
        //表面状态
        $surfaceState = ProductTypeModel::where('type', 5)->column('title', 'id');
        //成型工艺
        $moldingProcess = ProductTypeModel::where('type', 6)->column('title', 'id');
        return ZBuilder::make('form')
            ->addFormItems([
                ['hidden', 'id'],
                ['text', 'title', '轮毂名称'],
                ['select:6', 'series', '所属系列', '', $series],
                ['select:6', 'brand', '品牌', '', $brand],
                ['select:6', 'vehicle_type', '车辆类型', '', $vehicleType],
                ['select:6', 'motorcycle_type', '车型', '', $motorcycleType],
                ['image', 'cover', '图片'],
                ['text:6', 'part_number', '零件号'],
                ['number:6', 'price', '价格'],
                ['text:6', 'diameter', '尺寸（直径）'],
                ['text:6', 'width', '尺寸（宽度 J）'],
                ['text:6', 'et', 'ET'],
                ['text:6', 'olt_hole', '螺栓孔数'],
                ['text:6', 'pcd', 'pcd'],
                ['text:6', 'center_hole', '中心孔'],
                ['text:12', 'tire_width', '推荐轮胎'],
                ['text:6', 'load', '最大载荷'],
                ['text:6', 'weight', '重量KG'],
                ['number:6', 'sort', '排序', '', 100],
                ['number:6', 'stock', '库存', '', 100],
                ['select:6', 'surface_state', '表面状态', '', $surfaceState, '', 'multiple'],
                ['select:6', 'molding_process', '成型工艺', '', $moldingProcess, '', 'multiple'],
                ['radio', 'recommend', '推荐产品', '', [0 => '不推荐', 1 => '最新产品', 2 => '热销爆款']],
                ['image', 'recommend_img', '新品/爆款封面'],
                ['text', 'video_url', '视频链接'],
                ['ueditor', 'details', '详情图片'],
            ])
            ->setTrigger('recommend', '1,2', 'recommend_img')
            ->setFormData($info)
            ->fetch();
    }
}