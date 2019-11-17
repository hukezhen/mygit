<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\EmailSubscribe as EmailSubscribeModel;

class EmailSubscribe extends Admin
{


    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {

        $data_list = (new EmailSubscribeModel())->where($this->getMap())
            ->alias('a')
            ->order('a.id', 'desc')
            ->paginate();
        return ZBuilder::make('table')
            ->setTableName('email_subscribe', true)
            ->addTopButtons(['enable', 'disable', 'delete'])
            ->addColumns([
                ['id', 'ID', 'text'],
                ['email', '邮件地址', 'text'],
                ['ip', '订阅IP', 'text'],
                ['create_time', '订阅时间', 'datetime'],
                ['right_button', '操作', 'btn'],
            ])
            ->addRightButtons(['delete'])
            ->setRowList($data_list)
            ->fetch();
    }
}