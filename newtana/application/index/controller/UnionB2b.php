<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\index\controller;


use app\common\model\Orders;
use app\common\model\Payment as PaymentModel;
use think\facade\Env;
use think\facade\Log;
use zhangv\unionpay\UnionPay;

class UnionB2b extends Home
{
    protected function config()
    {
        return [
            'version' => '5.0.0',
            'signMethod' => '01', //RSA
            'encoding' => 'UTF-8',
            'merId' => '898111955320103',
            'currencyCode' => '156',
            'returnUrl' => $this->request->domain() . '/index/union_b2b/return.html',
            'notifyUrl' => $this->request->domain() . '/index/union_b2b/notify.html',
            'signCertPath' => Env::get('root_path') . 'config/union/private_b2b.pfx',
            'signCertPwd' => '123123', //签名证书密码
            'verifyCertPath' => Env::get('root_path') . 'config/union/b2b_acp_prod_verify_sign.cer',
            'encryptCertPath' => Env::get('root_path') . 'config/union/b2b_acp_prod_enc.cer',
            'ifValidateCNName' => true, //正式环境设置为true
        ];


    }

    /**
     * @param $sn
     * @param $moneys
     */
    public function index($sn, $moneys)
    {
        $unionPay = UnionPay::B2B($this->config(), 'prod');
        $html = $unionPay->pay($sn, $moneys);
        echo $html;
    }


    public function return()
    {

        try {
            $unionPay = UnionPay::B2B($this->config(), 'prod');
            $notifyData = $_REQUEST;
            $pay = PaymentModel::notify($notifyData['orderId'], $notifyData['traceNo']);
            if (true !== $pay) {
                throw new \Exception('支付失败');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage(), '/index.html');
        }
        $this->success('支付成功！', '/index.html');
    }


    public function notify()
    {
        $unionPay = UnionPay::B2B($this->config(), 'prod');
        try {
            $notifyData = $_POST;
            $respCode = $notifyData['respCode'];
            Log::info($notifyData);
            if ($respCode == '00') {
                //数据处理
                if (isset($notifyData['traceNo'], $notifyData['orderId'])) {

                    $pay = PaymentModel::notify($notifyData['orderId'], $notifyData['traceNo']);
                    if (true !== $pay) {
                        throw new \Exception('支付失败');
                    }
                }
            } elseif (in_array($respCode, ['03', '04', '05'])) {
                //后续需发起交易状态查询交易确定交易状态
                throw new \Exception($respCode);
            } else {
                throw new \Exception($respCode);
            }

        } catch (\Exception $e) {
            echo 'fail';
            exit;
        }
        echo 'success';
        exit;
    }
}