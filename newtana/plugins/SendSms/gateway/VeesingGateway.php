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

class VeesingGateway extends Gateway
{
    use HasHttpRequest;
    const URL = 'http://121.199.16.178/webservice/sms.php?method=Submit';

    /**
     *
     * Send a short message.
     *
     * @param PhoneNumberInterface $to
     * @param MessageInterface $message
     * @param Config $config
     * @return array|mixed
     * @throws GatewayErrorException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'account' => $config->get('account'),
            'password' => $config->get('password'),
            'mobile' => $to->getNumber(),
            'content' => $message->getContent($this)
        ];

        $result = $this->post(self::URL, $params);
        $result = json_decode(json_encode(simplexml_load_string($result)), true);
        if (!isset($result['code']) || $result['code'] != 2) {
            throw new GatewayErrorException('短信验证码发送失败！', $result['msg'], []);
        }
        return $result;
    }
}