<?php

/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2018 Xianzhou Technology (Chongqing) LLC.
 */

namespace plugins\SendSms\gateway;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Gateways\Gateway;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;

/**
 * Class QiruiGateway 启端云.
 */
class QiruiGateway extends Gateway
{
    use HasHttpRequest;
    const ENDPOINT_URL = 'http://api.qirui.com:7891/mt';

    /**
     * Send a short message.
     *
     * @param \Overtrue\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Overtrue\EasySms\Contracts\MessageInterface     $message
     * @param \Overtrue\EasySms\Support\Config                 $config
     *
     * @return array
     *
     * @throws GatewayErrorException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $timestamp = date('YmdHis');
        $params = [
            'un' => $config->get('username'),
            'da' => !\is_null($to->getIDDCode()) ? strval($to->getZeroPrefixedNumber()) : $to->getNumber(),
            'tf' => 1, //base64编码短信内容
            'sm' => base64_encode($message->getContent($this)),
            'rf' => 2, //json返回
            'dc' => 15, //0 表示英文，8 表示 UCS2，15 表示中文，指定内容类型
            'ts' => $timestamp, //时间
        ];

        $params['pw'] = $this->generateSign($params, $config->get('password'));

        $result = $this->get(self::ENDPOINT_URL, $params);

        if (!isset($result['id'])) {
            throw new GatewayErrorException('短信验证码发送失败！', $result['r'], []);
        }

        return $result;
    }

    /**
     * 获取秘钥.
     *
     * @param $params
     * @param $password
     *
     * @return string
     */
    public function generateSign($params, $password)
    {
        return base64_encode(md5($params['un'].$password.$params['ts'].$params['sm']));
    }
}
