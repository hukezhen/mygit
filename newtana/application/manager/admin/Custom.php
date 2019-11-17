<?php
/**
 *Created by PhpStorm.
 *User:she.
 *Date:2019/9/15.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\Custom as CustomModel;
use app\common\model\Product as ProductModel;
use think\Db;

class Custom extends Admin
{

    public function index()
    {

        $data_list = (new ProductModel())->where($this->getMap())
            ->where('mode',2)
            ->order('id', 'desc')
            ->paginate();


        return ZBuilder::make('table')
            ->setTableName('custom', true)
            ->addTopButtons(['add', 'enable', 'disable', 'delete'])// 添加顶部按钮
            ->setSearch(['title' => '分类名称'])
            ->addColumns([
                ['id', 'ID', 'text'],
                ['title', '定制标题', 'text'],
//                ['cover', '封面图', 'img_url'],
                ['price', '定制价格', 'text'],
                ['create_time', '添加时间', 'datetime'],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn'],
            ])
            ->setColumnWidth('id', 50)
            ->addRightButtons(['edit','delete'])
            ->setRowList($data_list)
            ->fetch();
    }


    public function add()
    {
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            // 验证
//            $result = $this->validate($data, 'Custom');
//            if (true !== $result) {
//                $this->error($result);
//            }
            $data['mode'] = 2;
            Db::startTrans();
            try {
                $usersInfo = ProductModel::create($data, true);
                if (false === $usersInfo) {
                    throw new \Exception('添加失败！');
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $this->success('添加成功', 'index');

        }


        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['text', 'title', '定制标题'],
                ['number', 'price', '价格'],
                ['text', 'state', '表面状态'],
                ['image', 'cover', '封面图'],
            ])
            ->fetch();
    }



    public function edit($id = '')
    {

        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            // 验证
            Db::startTrans();
            try {
                $usersInfo = ProductModel::update($data, true);
                if (false === $usersInfo) {
                    throw new \Exception('添加失败！');
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $this->success('添加成功', 'index');

        }

        $model = ProductModel::get($id);

        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['hidden','id'],
                ['text', 'title', '定制标题'],
                ['number', 'price', '价格'],
                ['text', 'state', '表面状态'],
                ['image', 'cover', '封面图'],
            ])
            ->setFormData($model)
            ->fetch();
    }


}

