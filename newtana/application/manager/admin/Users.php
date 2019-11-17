<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\Users as UsersModel;

class Users extends Admin
{
    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {

        $data_list = (new UsersModel())->where($this->getMap())
            ->order('uid', 'desc')
            ->paginate();


        return ZBuilder::make('table')
            ->setTableName('users', true)
            ->addTopButtons(['add','enable', 'disable', 'delete'])
            ->addTopSelect('type', '账号类型', [0 => '个人', 1 => '车企', 2 => '经销商'])
            ->setSearch(['telephone' => '手机号码', 'nickname' => '用户名称'])
            ->addColumns([
                ['uid', 'ID', 'text'],
                ['face', '头像', 'picture'],
                ['type', '类型', 'text'],
                ['username', '用户信息', 'callback', function ($value, $data) {
                    return "名称:{$data['nickname']} 
<br> 联系手机：{$data['telephone']}
<br> 联系座机：{$data['phone']}
<br> 电子邮箱：{$data['email']}
<br> 年均销量：{$data['sales']}
<br> 收货地址：{$data['address']}
";
                }, '__data__'],
                ['consumption', '消费总金额', 'callback', function ($value) {
                    return "<span style='color: red'>" . number_format($value, 2) . "</span>";
                }],
                ['create_time', '注册时间', 'datetime'],
                ['status', '禁止登录', 'switch'],
                ['right_button', '操作', 'btn'],
            ])
            ->setColumnWidth('uid', 30)
            ->setColumnWidth('face', 50)
            ->setColumnWidth('type', 30)
            ->setColumnWidth('consumption', 55)
            ->setColumnWidth('status', 40)
            ->setColumnWidth('right_button', 30)
            ->setColumnWidth('create_time', 75)
            ->setColumnWidth('username', 155)
            ->addRightButtons(['edit','delete'])
            ->setRowList($data_list)
            ->setPrimaryKey('uid')
            ->fetch();
    }

    public function add()
    {

        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();

            // 验证
            $result = $this->validate($data, 'Users');
            if (true !== $result) {
                $this->error($result);
            }

            if ($link = UsersModel::create($data)) {
                // 记录行为
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }

        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['radio', 'type', '账号类型', '', UsersModel::getTypeData()],
                ['text', 'username', '用户账号'],
                ['text', 'nickname', '名称'],
                ['password', 'password', '密码'],
                ['image', 'face', '头像'],
                ['text', 'telephone', '手机'],
                ['text', 'phone', '座机'],
                ['text', 'email', '邮箱'],
                ['text', 'address', '地址'],
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
            $result = $this->validate($data, 'Users');
            if (true !== $result) {
                $this->error($result);
            }

            if ($link = UsersModel::update($data)) {
                // 记录行为
                $this->success('操作成功', 'index');
            } else {
                $this->error('操作失败');
            }
        }

        $info = UsersModel::get($id)->getData();

        unset($info['password']);
        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['hidden','uid'],
                ['radio', 'type', '账号类型', '', UsersModel::getTypeData()],
                ['text', 'username', '用户账号'],
                ['text', 'nickname', '名称'],
                ['password', 'password', '密码'],
                ['image', 'face', '头像'],
                ['text', 'telephone', '手机'],
                ['text', 'phone', '座机'],
                ['text', 'email', '邮箱'],
                ['text', 'address', '地址'],
            ])
            ->setFormData($info)
            ->fetch();
    }


}