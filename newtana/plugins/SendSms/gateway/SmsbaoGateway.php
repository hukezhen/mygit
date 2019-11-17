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
 * 短信宝  http://www.smsbao.com
 * Class SmsbaoGateway.
 */
class SmsbaoGateway extends Gateway
{
    use HasHttpRequest;
    const ENDPOINT_URL = ' https://api.smsbao.com/sms';

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
        $params = [
            'u' => $config->get('username'),
            'p' => md5($config->get('password')),
            'm' => !\is_null($to->getIDDCode()) ? strval($to->getZeroPrefixedNumber()) : $to->getNumber(),
            'c' => urlencode($message->getContent($this)),
        ];
        $result = $this->get(self::ENDPOINT_URL, $params);

        if ('0' != $result) {
            throw new GatewayErrorException('短信验证码发送失败！', $result, []);
        }

        return $result;
    }
}
