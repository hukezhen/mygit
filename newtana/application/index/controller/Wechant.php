<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\index\controller;


use app\common\model\Orders;
use app\common\model\Payment as PaymentModel;

class Wechant extends Home
{

    protected $appId = '155862353104618';

    protected $appKey = 'rlVByfxpd3rCvU92sOr5M12fXracjCcz';
    protected $appWapKey = 'cqIHSowCoUtYebwgLTx90PNOyjG0uHj4';

    protected $mpAppId = '156082746966469';
    protected $mpKey = 'tgYSE8zHFuOkPipPgLIoikVYNZUqLIr1';

    /**
     * @param $sn
     * @param $moneys
     */
    public function index($sn, $moneys, $mode = 0)
    {
        if ($mode == 0) {
            $req = [];
            $req['appId'] = $this->appId;
            $req['funcode'] = 'WP001';
            $req['mhtOrderNo'] = $sn;
            $req['mhtOrderName'] = 'tana';
            $req['mhtOrderType'] = '01';
            $req['mhtCurrencyType'] = '156';
            $req['mhtOrderAmt'] = $moneys * 100;
            $req['mhtOrderDetail'] = 'Tana-产品购买';
            $req['mhtOrderTimeOut'] = 3600;
            $req['mhtOrderStartTime'] = date('YmdHis');
            $req['notifyUrl'] = request()->domain() . '/index/wechant/notify.html';
            $req['frontNotifyUrl'] = request()->domain() . '/index.html';
            $req['mhtCharset'] = 'UTF-8';
            $req['deviceType'] = '08';
            $req['payChannelType'] = '13';
            $req['mhtReserved'] = 'test';
            $req['mhtSignType'] = 'MD5';
            $req['version'] = '1.0.0';
            $req['outputType'] = '0';
            $req['mhtSignature'] = $this->getSignTrue($req, $this->appKey);
            $data = $this->getSignTrue($req, 'post');
            $res = $this->post('https://pay.ipaynow.cn', $data);
            $tn = '';
            if ($res != '' && stripos($res, '&tn=') && $req['outputType'] == '0') {
                $arr = explode('&', $res);
                foreach ($arr as $v) {
                    $value = explode('=', $v);
                    if ($value[0] == 'tn') {
                        $tn = urldecode($value[1]);
                        $this->success('', '', ['img' => $tn]);
                    }
                }
            } else {
                parse_str($res, $str);
                $this->error($str['responseMsg']);
            }
        } elseif ($mode == 2) {
//            if($moneys > 2000){
//                $this->error('单笔超过2000不能使用微信公众号支付！');
//            }
            $req['appId'] = $this->mpAppId;
            $req['funcode'] = 'WP001';
            $req['mhtOrderNo'] = $sn;
            $req['mhtOrderName'] = '产品购买';
            $req['mhtOrderType'] = '01';
            $req['mhtCurrencyType'] = '156';
            $req['mhtOrderAmt'] = $moneys * 100;;
            $req['mhtOrderDetail'] = '轮毂购买服务';
            $req['mhtOrderTimeOut'] = 3600;
            $req['mhtOrderStartTime'] = date('YmdHis');
            $req['notifyUrl'] = request()->domain() . '/index/wechant/notify/type/2.html';
            $req['frontNotifyUrl'] = request()->domain() . '/index.html';;
            $req['mhtCharset'] = 'UTF-8';
            $req['deviceType'] = '0600';
            $req['payChannelType'] = '13';   //微信13    支付宝12
            $req['mhtReserved'] = 'test';
            $req['mhtSignType'] = 'MD5';
            $req['version'] = '1.0.0';
            $req['outputType'] = '0';    //0 直接调起    1 返回支付凭证
            $req['mhtSignature'] = $this->getSignTrue($req, $this->mpKey);
            $data = $this->getSignTrue($req, 'post');
            header('location:https://pay.ipaynow.cn?' . $data);
        } else {
            $req = array();
            $req["appId"] = "155892822802558";
            $req["deviceType"] = '0601';
            $req['frontNotifyUrl'] = request()->domain() . '/index.html';
            $req["funcode"] = "WP001";
            $req["mhtCharset"] = "UTF-8";
            $req["mhtCurrencyType"] = "156";
            $req["mhtOrderAmt"] = $moneys * 100;
            $req["mhtOrderDetail"] = "轮毂购买服务";
            $req["mhtOrderName"] = "产品购买";
            $req["mhtOrderNo"] = $sn;
            $req["mhtOrderStartTime"] = date("YmdHis");
            $req["mhtOrderTimeOut"] = "3600";
            $req["mhtOrderType"] = "01";
            $req["mhtReserved"] = "test";
            $req["mhtSignType"] = "MD5";
            $req['notifyUrl'] = request()->domain() . '/index/wechant/notify/type/1.html';
            $req["outputType"] = '0';//   0 默认值    // 2  微信deeplink模式
            $req["payChannelType"] = "13"; //12 支付宝  //13 微信 //20 银联  //25  手Q
            $req["version"] = "1.0.0";
            $req["consumerCreateIp"] = request()->ip();
            $str = $this->getToStr($req, $this->appWapKey);
            header("location:https://pay.ipaynow.cn?" . $str);
            die();
        }
    }


    public function notify($type = 0)
    {
        $res = file_get_contents('php://input');
        $Arr = array();
        $res = explode('&', $res);
        foreach ($res as $v) {
            $value = explode('=', $v);
            $Arr[$value[0]] = urldecode($value[1]);
        }

        $signatrue = $this->getSignTrue($Arr, $type == 0 ? $this->appKey : ($type == 2 ? $this->mpKey : $this->appWapKey));

        if ($signatrue == $Arr['signature']) {
            $pay = PaymentModel::notify($Arr['mhtOrderNo'], $Arr['nowPayOrderNo']);
            if (true !== $pay) {
                echo 'success=N';
            }
            echo 'success=Y';
        } else {
            //异步通知验签失败，在此处写业务逻辑
            echo 'success=N';
        }

    }


    /**
     * 处理微信手机支付
     * @param $arr
     * @return array
     */
    protected function getDate($arr)
    {
        $result = array();
        $funcode = $arr['funcode'];
        foreach ($arr as $key => $value) {
            if (($funcode == 'WP001') && !($key == 'mhtSignature' || $key == 'signature')) {
                $result[$key] = $value;
                continue;
            }
            if (($funcode == 'N001' || $funcode == 'N002') && !($key == 'signature')) {
                $result[$key] = $value;
                continue;
            }
            if (($funcode == 'MQ002') && !($key == 'mhtSignature' || $key == 'signature')) {
                $result[$key] = $value;
                continue;
            }
        }
        return $result;
    }

    protected function createSignaTure($arr, $key)
    {
        $date = $this->getDate($arr);
        ksort($date);
        $str = '';
        foreach ($date as $k => $v) {
            if ($v != '') {
                $str .= $k . '=' . $v . '&';
            }
        }
        $str .= strtolower(md5($key));
        return (strtolower(md5($str)));
    }

    protected function getToStr($arr, $key)
    {
        $date = $arr;
        $info = $this->createSignaTure($arr, $key);
        $str = '';
        foreach ($date as $k => $v) {
            if ($v != '') {
                $str .= $k . '=' . urlencode($v) . '&';
            }
        }
        $str .= 'mhtSignature=' . $info;
        return $str;
    }


    /**
     * @param $url
     * @param $data
     * @return bool|string
     */
    protected function post($url, $data)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 40); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            return 'Errno' . curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

    /**
     * @param $Arr
     * @param $key
     * @return bool|string
     */
    protected function getSignTrue($Arr, $key)
    {
        if (!empty($Arr)) {
            ksort($Arr);
            $str = '';
            foreach ($Arr as $k => $v) {
                if ($v == '' || $k == 'signature') {
                    continue;
                }
                $str .= $k . '=' . $v . '&';
            }
            if ($key == 'post') {
                return substr($str, 0, -1);
            } else {
                return strtolower(md5($str . md5($key)));
            }
        }
        return false;
    }


}