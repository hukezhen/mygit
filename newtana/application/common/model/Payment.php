<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\model;


use think\Db;
use think\Model;

class Payment extends Model
{
    public $autoWriteTimestamp = true;


    public static function notify($out_trade_no, $trade_no)
    {
        try {
            $payment = self::get(['out_trade_no' => $out_trade_no]);
            if (!$payment || $payment['status'] == 1) {
                return true;
            }
            $order = Orders::get($payment['orders_id']);
            if (!$order || $order['status'] != 1) {
                return true;
            }
            $order->status = 2;
            $order->payment_time = time();
            $order->trade_no = $trade_no;
            $order->out_trade_no = $out_trade_no;
            if (false === $order->save()) {
                throw new \Exception('订单更新失败');
            }
            $payment->payment_time = time();
            $payment->status = 1;
            $payment->trade_no = $trade_no;
            if (false === $payment->save()) {
                throw new \Exception('支付更新失败！');
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return true;
    }
}