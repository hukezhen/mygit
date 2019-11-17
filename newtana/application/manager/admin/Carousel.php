<?php

/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2018 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\Carousel as CarouselModel;

class Carousel extends Admin
{
    /**
     * 首页.
     *
     * @return mixed
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $data_list = (new CarouselModel())->where($this->getMap())
            ->order('id', 'desc')
            ->paginate();

        return ZBuilder::make('table')
            ->setTableName('carousel', true)
            ->addTopButtons(['add', 'enable', 'disable', 'delete'])// 添加顶部按钮
            ->addColumns([
                ['id', 'ID', 'text'],
                ['cover', '封面', 'picture'],
                //['url', '跳转地址', 'text.edit'],
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
            $result = $this->validate($data, 'Carousel');
            if (true !== $result) {
                $this->error($result);
            }
            $data['status'] = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
            $data['cover_path'] = get_file_path($data['cover']);

            if ($link = CarouselModel::create($data)) {
                // 记录行为
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }

        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['select', 'type', '轮播图位置', '', [1 => '首页', 2 => '爆款推荐']],
                ['image', 'cover', '封面'],
                ['text', 'url', '跳转地址'],
                ['textarea', 'title', '标题'],
                ['text', 'sort', '排序', '', 100],
                ['switch', 'status', '是否开启', '', '1'],
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
            $result = $this->validate($data, 'Carousel');
            if (true !== $result) {
                $this->error($result);
            }
            $data['status'] = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
            !empty($data['cover']) && $data['cover_path'] = get_file_path($data['cover']);
            if ($link = CarouselModel::update($data)) {
                // 记录行为
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        $info = CarouselModel::get($id);

        return ZBuilder::make('form')
            ->addFormItems([
                ['hidden', 'id'],
                ['select', 'type', '轮播图位置', '', [1 => '首页', 2 => '爆款推荐']],
                ['image', 'cover', '封面'],
                ['text', 'url', '跳转地址'],
                ['textarea', 'title', '标题'],
                ['text', 'sort', '排序'],
                ['switch', 'status', '是否开启'],
            ])
            ->setFormData($info)
            ->fetch();
    }
}
