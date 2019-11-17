<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace plugins\SendSms\gateway;


use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Gateways\Gateway;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;

class IpaynowGateway extends Gateway
{
    use HasHttpRequest;
    const ENDPOINT_URL = 'https://sms.ipaynow.cn';

    /**
     * Send a short message.
     *
     * @param \Overtrue\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Overtrue\EasySms\Contracts\MessageInterface $message
     * @param \Overtrue\EasySms\Support\Config $config
     *
     * @return array
     * @throws GatewayErrorException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {

        $templateInfo = json_encode([
            'tempCode' => $message->getTemplate($this),
            'param' => $message->getData($this)
        ]);
        $templateInfo = str_replace('"', "'", $templateInfo);
        $params = [
            'funcode' => 'INDUSTRY_TEMP',
            'appId' => $config->get('appId'),
            'mhtOrderNo' => date('YmdHis').rand(1000,9999),
            'mobile' => $to->getNumber(),
            'templateInfo' => $templateInfo,
            'notifyUrl' => $config->get('notifyUrl'),
        ];

        $params = $this->sign($params, $config->get('des_key'), $config->get('md5'));
        $result = $this->post(self::ENDPOINT_URL,$params);
        $result = explode('|',$result);
        if (!isset($result[0]) ||  '1' != $result[0]) {
            throw new GatewayErrorException('短信验证码发送失败',0);
        }
        $result = $this->decrypt($result[1],$config->get('des_key'));
        parse_str($result,$res);
        if (!isset($res['responseCode']) || $res['responseCode'] != '00'){
            throw new GatewayErrorException('发送失败', 0);
        }
        return $res;
    }

    /**
     * 获取请求报文
     * @param $params
     * @param $des_key
     * @param $md5
     * @return string|array
     */
    public function sign($params, $des_key, $md5)
    {
        $msg = '';
        ksort($params);
        foreach ($params as $k => $v) {
            $msg .= $k . '=' . $v . '&';
        }
        $msg = substr($msg, 0, -1);
        $message_data_one = base64_encode('appId=' . $params['appId']);
        $message_data_two = base64_encode($this->encrypt($msg, $des_key));
        $message_data_three = base64_encode(md5($msg . '&' . $md5));
        $message = urlencode($message_data_one . '|' . $message_data_two . '|' . $message_data_three);
        return [
            'funcode' =>   $params['funcode'],
            'message' => $message
        ];
    }

    /**
     * 加密
     * @param $data
     * @param $des_key
     * @return false|string
     */
    public function encrypt($data, $des_key)
    {
        if (strlen($data) % 8) {
            $data = str_pad($data, strlen($data) + 8 - strlen($data) % 8, "\0");
        }

        return openssl_encrypt(
            $data,
            'DES-EDE3',
            $des_key,
            OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,
            ''
        );
    }

    /**
     * 解密
     * @param $data
     * @param $des_key
     * @return false|string
     */
    public function decrypt($data, $des_key)
    {
        return openssl_decrypt(
            base64_decode($data),
            'DES-EDE3',
            $des_key,
            OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);

    }

}