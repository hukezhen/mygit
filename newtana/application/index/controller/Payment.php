<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\index\controller;


use app\common\model\Orders;
use app\common\model\Payment as PaymentModel;

class Payment extends Home
{

    public function index($id, $type = 1, $mode = 0)
    {

        if (empty(session('users.uid'))) {
            $this->error('请先登录');
        }
        $order = Orders::get($id);

        if ($order['status'] != 1) {
            $this->error('订单已经支付成功');
        }
        $sn = time() . $id . rand(1000, 9999);

        $data = [
            'uid' => session('users.uid'),
            'orders_id' => $id,
            'out_trade_no' => $sn,
            'mode' => $type
        ];

        if (false === PaymentModel::create($data)) {
            $this->error('订单支付失败！');
        }

        if ($type == 1) {
            $this->redirect('/index/alipay/index', ['sn' => $sn, 'moneys' => $order['moneys'], 'mode' => $mode]);
        } elseif ($type == 2) {
            $this->redirect('/index/wechant/index', ['sn' => $sn, 'moneys' => $order['moneys'], 'mode' => $mode]);
        } else if ($type == 3) {
            $this->redirect('/index/union/index', ['sn' => $sn, 'moneys' => bcmul($order['moneys'], 100,0), 'mode' => $mode]);
        } else if ($type == 4) {
            $this->redirect('/index/union_b2b/index', ['sn' => $sn, 'moneys' => bcmul($order['moneys'], 100,0), 'mode' => $mode]);
        }
    }

}