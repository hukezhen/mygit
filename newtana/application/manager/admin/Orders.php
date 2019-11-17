<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\common\model\Orders as OrdersModel;

class Orders extends Admin
{

    /**
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {

        $data_list = OrdersModel::with('manager')
            ->alias('aa')
            ->field('aa.*,ab.telephone')
            ->leftJoin('users ab', 'aa.uid=ab.uid')
            ->where('aa.state', 1)
            ->order('aa.update_time','desc')
            ->paginate();
        return ZBuilder::make('table')
            ->setTableName('orders')
            ->setSearch(['b.telephone' => '用戶手机号码'])
            ->addTimeFilter('a.create_time')
            ->addTopButtons(['delete'])
            ->addColumns([
                ['id', '订单', 'text'],
//                ['uid', '用户UID', 'text'],
                ['telephone', '手机号码', 'text'],
                ['update_time', '产品信息', 'callback', function ($value, $data) {
                    $detail = '';
                    foreach ($data['manager'] as $k => $v) {
                        $detail .= "{$v['part_number']} x <b style='color: red;'>{$v['number']}</b><br>";
                    }
                    return $detail;
                }, '__data__'],
                ['moneys', '订单金额', 'text.edit'],
                ['address', '收货地址', 'callback',function($value){
                    $address = json_decode($value,true);
                    return '收货人:'.$address['name'].'<br>'.
                        '电话:'.$address['postal_code'].'<br>'.
                        '地址:'.$address['region'].$address['address'];
                }],
                ['invoice', '发票信息', 'callback',function($value){
                    $invoice = @json_decode($value,true);
                    if (empty($invoice) || $invoice['invoice'] == 0){
                        return '不开发票！';
                    }else{
                        return '发票类型:'.($invoice['invoice'] == 1 ? '普通发票' : '专用发票' ).'<br>'.
                            '公司名称:'.$invoice['invoice_company'].'<br>'.
                            '税号:'.$invoice['invoice_tax'].'<br>'.
                            '开户行:'.$invoice['invoice_bank'].'<br>'.
                            '银行账号:'.$invoice['invoice_card'].'<br>';
                    }
                }],
                ['txt', '备注', 'text'],
                ['create_time', '下单时间', 'callback', function ($value) {
                    return date('Y/m/d H:i:s', $value);
                }],
                ['status', '状态', 'status', '', [1 => '待付款:warning', 2 => '待发货:info', 3 => '已发货:primary', 4 => '已完成:success']],
                ['right_button', '操作', 'btn'],
            ])
            ->addRightButton('payment', [
                'title' => '已付款',
                'icon' => 'fa fa-fw fa-check',
                'data-title' => '确认要标记订单为已付款吗？',
                'style' => 'width:80px;',
                'class' => 'btn btn-xs btn-warning ajax-get confirm',
                'href' => url('auth', ['id' => '__id__', 'type' => 2])
            ], false, [
                'title' => 'true',
//                'icon' => 'false',
                // 按钮大小：xs/sm/lg，留空则为普通大小
//                'size' => 'xs',
//                // 按钮样式：default/primary/success/info/warning/danger
//                'style' => 'default'
            ])
            ->addRightButton('delivery', [
                'title' => '已发货',
                'icon' => 'fa fa-fw fa-check',
                'data-title' => '确认要标记订单为已发货吗？',
                'style' => 'width:80px;',
                'class' => 'btn btn-xs btn-info ajax-get confirm',
                'href' => url('auth', ['id' => '__id__', 'type' => 3])
            ], false, [
                'title' => 'true'
            ])
            ->replaceRightButton(['status' => ['in', '1']], '', ['delivery'])
            ->replaceRightButton(['status' => ['in', '2']], '', ['payment'])
            ->replaceRightButton(['status' => ['in', '3,4']], '', ['payment', 'delivery'])
            ->setRowList($data_list)
            ->setColumnWidth('id', 60)
            ->setColumnWidth('status', 60)
            ->fetch();
    }


    /**
     * 投資通過
     */
    public function auth($id, $type = 2)
    {
        $order = OrdersModel::where('id', $id)->update(['status' => $type]);
        if (false === $order) {
            $this->error('标记失败！');
        }
        $this->success('标记成功', 'index');

    }


    public function custom()
    {

        $data_list = (new OrdersModel())->where($this->getMap())
            ->order('a.id', 'desc')
            ->alias('a')
            ->field('a.*,b.telephone,c.part_number,c.title,c.state,d.number')
            ->leftJoin('users b', 'a.uid=b.uid')
            ->leftJoin('orders_detail d','d.orders_id = a.id')
            ->leftJoin('product c', 'd.pid=c.id')

            ->where('a.state', '=', 2)
            ->paginate();

        return ZBuilder::make('table')
            ->setTableName('order')
            ->setSearch(['b.telephone' => '用戶手机号码'])
            ->addTimeFilter('a.create_time')
            ->addTopButtons(['delete'])
            ->addColumns([
                ['id', '订单ID', 'text'],
//                ['uid', '用户UID', 'text'],
                ['telephone', '手机号码', 'text'],
//                ['part_number', '产品型号', 'text'],
                ['title', '工艺及颜色', 'text'],
                ['banner', '品牌', 'text'],
                ['marking', '型号', 'text'],
                ['number', '数量', 'text'],
                ['moneys', '金额', 'text'],
                ['create_time', '下单时间', 'callback', function ($value) {
                    return date('Y/m/d H:i:s', $value);
                }],
                ['invoice', '发票信息', 'callback',function($value){
                    $invoice = @json_decode($value,true);
                    if (empty($invoice) || $invoice['invoice'] == 0){
                        return '不开发票！';
                    }else{
                        return '发票类型:'.($invoice['invoice'] == 1 ? '普通发票' : '专用发票' ).'<br>'.
                            '公司名称:'.$invoice['invoice_company'].'<br>'.
                            '税号:'.$invoice['invoice_tax'].'<br>'.
                            '开户行:'.$invoice['invoice_bank'].'<br>'.
                            '银行账号:'.$invoice['invoice_card'].'<br>';
                    }
                }],
                ['txt', '备注', 'text'],
                ['status', '状态', 'status', '', [1 => '待付款:warning', 2 => '待发货:info', 3 => '已发货:primary', 4 => '已完成:success']],
                ['right_button', '操作', 'btn'],
            ])
            ->addRightButton('payment', [
                'title' => '已付款',
                'icon' => 'fa fa-fw fa-check',
                'data-title' => '确认要标记订单为已付款吗？',
                'style' => 'width:80px;',
                'class' => 'btn btn-xs btn-warning ajax-get confirm',
                'href' => url('auth', ['id' => '__id__', 'type' => 2])
            ], false, [
                'title' => 'true',
//                'icon' => 'false',
                // 按钮大小：xs/sm/lg，留空则为普通大小
//                'size' => 'xs',
//                // 按钮样式：default/primary/success/info/warning/danger
//                'style' => 'default'
            ])
            ->addRightButton('delivery', [
                'title' => '已发货',
                'icon' => 'fa fa-fw fa-check',
                'data-title' => '确认要标记订单为已发货吗？',
                'style' => 'width:80px;',
                'class' => 'btn btn-xs btn-info ajax-get confirm',
                'href' => url('auth', ['id' => '__id__', 'type' => 3])
            ], false, [
                'title' => 'true'
            ])
            ->replaceRightButton(['status' => ['in', '1']], '', ['delivery'])
            ->replaceRightButton(['status' => ['in', '2']], '', ['payment'])
            ->replaceRightButton(['status' => ['in', '3,4']], '', ['payment', 'delivery'])
            ->setColumnWidth('id', 60)
            ->setColumnWidth('status', 60)
            ->setRowList($data_list)
            ->fetch();
    }


}