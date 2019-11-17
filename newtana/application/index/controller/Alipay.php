<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\index\controller;


use app\common\model\Orders;
use think\Exception;
use think\facade\Env;
use think\facade\Log;
use Yansongda\Pay\Pay;
use app\common\model\Payment as PaymentModel;

class Alipay extends Home
{

    protected function config()
    {
        return [
            'app_id' => '2019051564604389',
            'notify_url' => request()->domain() . '/index/alipay/notify.html',
            'return_url' => request()->domain() . '/index/alipay/return.html',
            'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5yzMuSuwCu4Gkg/sePWFuZ37yYnzpirWSGTQpXZluDkLyLzkpRTHdwOaHc6adW2oLxV+46lrfSmqHJX1QHeuRerHTFX6Z21LN35Io0GuqHa1+p7bxrweJywGIUqXKrP2mSpHUoNJ/xZVrN2noykA+t8S4at/D3FLBsASvhlLdV96Hr3SVBrvrpSQBZgVk/s0xasS1ellIr3i1Jv/R2X8uWrEbtM4yy2qvl/efZ1Q2YqZ1FDYBOdyQ41/0Sal9Mmo1O3cPYoxpSQ4Mo09YSroEk+EgfuV/WZgcJc4sXDgzhL+kRmI/+APov78jGqGpLOyGvktvpRiM504GkVmMBLpGwIDAQAB',
            'private_key' => 'MIIEowIBAAKCAQEA09VGKbnCykaageKax0DS0ARkNKdz/oJt7JLq+wQ6rqn7GkaalDcmRvrde18Dt6CiQMjuiIDL3s0wBV4aqEcG//tIaYrYOj3RLe799VeOcwQ4hK0eMlZHd5KgYZhd6XOJBoRkunmbcjunjSdZYbmcKgTwS0x0Nkkm3dE7q4ojqP0BC3nMweRqY04YeijxiQOQDB/ly3TSTdzKroe4gxEOfogvKbqdgt2P50Vra6buEzMrvp+9NQorYdh/apAeTR2IyGwiixK6XV+eSmF+XXTmyMh5GlBjqbxPyO3PmngGVeMjkGQYC2vnMQlwSqPtt4NDcW6W87EGoV7SQBDvCkWYNwIDAQABAoIBAAeMHr7X1n/ceh7MkxUsUxaEQYm6uaBEx2PZbDPbV7fnaIHsKA/F00BOKdv+a6R8PauvPQhyBlF+4Aog6zQ5q58tNcxE3SVeFHjdfgDiyLmCWf0oKd5DQrKY78vqYNYzfptT1j7qnEY6p+KYl0+rDExW9rTIGY/hlVfr34NiHZkkseotzWyGzL51SMyKm17jBTtrZq+m4/qKQSRJV9c0AzoRS9ipWih+qjicPx05zdv1kCeW+ybUqwy9rrHSlb/S/uQwO9YNsY+SdeG5IPY9G8BtYMYN1AEbb3bEEjNxXVdmbeXvbuZlUbfOEoYxcudj33WUTucZhlmR8U2hlRDojnkCgYEA9RnBxGLaAIoc8pNflZUWlYIMRL14K0usiZL6xstBtOxcipl36XAW23rwCWTY80bb56A2U9Krq7yk1fjlwiTlHYIJROYbBHbiPSnBk0IPx7C9TnVQ6E+m7ervRK6OU6OdWNXXxhX/GjRoWTUIhfFI/6yeVU741SbUXZTUPphs0dUCgYEA3UDMI76CU1hByNb0WJwQfNkqF8jaYHck/yg3FdaGLPC6nhmAxvKQBw77ZuiN1VQiMJY6oBz9Q709z26stZNAyFXw13kqL3Ufs1WT5Jkjx4uyArCNrF3DT91jMS7WpSCXcCEhjYKAoeLjRjLiABOLoZFPYZzHYiWKeDlWfKSKO9sCgYBIN5SeNrohGpC48K9jNZB38N+IiYMUzrFBv+rgFBeCJXqG4/0u7PXvKWP8gJhj5eb5cn1PaW1npqey3ClmcNSJQz1T7bDcOeMjPmBo8TgREIRWz75NFNsMkwJ47C3HexfriuaO2QBXSkXMsxGV1ag6nTPx0tkChPRhtm5rGyJFGQKBgQDbIgtNt6kCfR4imetGO8QEs12CUxcehcKpyJ3OTjt0FTTv01h9Ms69fvQ2N0wKppp6PsBvxmZ9CdhHM1lxORfnk17HOqUyfbFZAABLX/VU9KHpEg4vUmpYFm/gzYAf6gizMM03h7J5I+tvooO+CZ249t+FUS8UoRPlAP0M1DZpEwKBgFzhVFWfmDMRjOVadHKIzQVx/fJ3aj8jhx0WOSpff2rTVwJnS4qyA43ZLNcj2IGSmgiQCnOF0Zmbvmpo7MKnnCtoZHpwbigsKjSsQi+hdnQeiXhPky3HsRk+E8bZzyxrvvChve48XyB06YNyVweh5yTfZdJ5IlK2hcqyWeJYKRxq',
            'log' => [ // optional
                'file' => Env::get('runtime_path') . '/alipay.log',
                'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'single', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http' => [ // optional
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
            ],
        ];
    }

    public function index($sn = '', $moneys = '', $mode = 0)
    {
        $order = [
            'out_trade_no' => $sn,
            'total_amount' => $moneys,
            'subject' => 'Tana-商品购买',
        ];
        if ($mode == 1) {
            $alipay = Pay::alipay($this->config())->wap($order);
        } else {
            $alipay = Pay::alipay($this->config())->web($order);
        }

        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }

    /**
     * @throws \Exception
     */
    public function return()
    {
        $data = Pay::alipay($this->config())->verify(); // 是的，验签就这么简单！
        if (isset($data->out_trade_no, $data->trade_no)) {
            $pay = PaymentModel::notify($data->out_trade_no, $data->trade_no);
            if (true === $pay) {
                $this->success('支付成功！', '/index.html');
            }
        }
        $this->error('支付失败！', '/index.html');


        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config());

        try {
            $data = $alipay->verify(); // 是的，验签就这么简单！
            if ($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED') {
                $pay = PaymentModel::notify($data->out_trade_no, $data->trade_no);
                if (true !== $pay) {
                    throw new Exception($pay);
                }
            }
            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $alipay->success()->send();// laravel 框架中请直接 `return $alipay->success()`
    }

}