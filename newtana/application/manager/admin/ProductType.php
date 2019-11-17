<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\ProductType as ProductTypeModel;

class ProductType extends Admin
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
        $data_list = (new ProductTypeModel())->where($this->getMap())
            ->order('id', 'desc')
            ->paginate();

        return ZBuilder::make('table')
            ->setTableName('product_type', true)
            ->addTopButton('add', ['href' => url('add')], true) // 添加单个顶部按钮
            ->addTopButtons(['enable', 'disable', 'delete'])// 添加顶部按钮
            ->addTopSelect('type', '产品字典类型', ProductTypeModel::getTypeData())
            ->setSearch(['title' => '字段名称'])
            ->addColumns([
                ['id', 'ID', 'text'],
                ['type', '产品字典类型', 'text'],
                ['title', '字段名称', 'text'],
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

    /**
     * @return mixed
     *
     * @throws \think\Exception
     */
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();

            // 验证
            $result = $this->validate($data, 'ProductType');
            if (true !== $result) {
                $this->error($result);
            }
            $data['status'] = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;

            if ($link = ProductTypeModel::create($data)) {
                // 记录行为
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }

        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['radio', 'type', '产品字典类型', '', ProductTypeModel::getTypeData()],
                ['text', 'title', '字段名称'],
                ['radio', 'multiple', '是否多选', 0, [1 => '是', 0 => '否'],0],
                ['text', 'sort', '排序','',100],
                ['switch', 'status', '是否启用','',1],
            ])
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
            // 验证
            $result = $this->validate($data, 'ProductType');
            if (true !== $result) {
                $this->error($result);
            }
            $data['status'] = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
            if ($link = ProductTypeModel::update($data)) {
                // 记录行为
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        $info = ProductTypeModel::get($id)->getData();

        return ZBuilder::make('form')
            ->addFormItems([
                ['hidden', 'id'],
                ['radio', 'type', '产品字典类型', '', ProductTypeModel::getTypeData()],
                ['text', 'title', '字段名称'],
                ['radio', 'multiple', '是否多选', 0, [1 => '是', 0 => '否']],
                ['text', 'sort', '排序'],
                ['switch', 'status', '是否启用'],
            ])
            ->setFormData($info)
            ->fetch();
    }
}