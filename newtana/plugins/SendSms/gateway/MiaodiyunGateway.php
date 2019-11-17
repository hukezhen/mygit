<?php

/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2018 Xianzhou Technology (Chongqing) LLC.
 * @date {datetime}
 */

namespace plugins\SendSms\gateway;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Gateways\Gateway;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;

/**
 * 秒滴云短信
 * Class MiaodiyunGateway
 * @package plugins\sendSms\gateway
 */
class MiaodiyunGateway extends Gateway
{
    use HasHttpRequest;

    /**
     * @var string url中的accountSid。如果接口验证级别是主账户则传网站“个人中心”页面的“账户ID”，
     */
    const BASE_URL = 'https://api.miaodiyun.com/20150822/industrySMS/sendSMS';
    /**
     *
     * /**
     * @var string 请求类型
     */
    const CONTENT_TYPE = 'application/x-www-form-urlencoded';
    /**
     * @var string 期望服务器响应的内容类型，可以是application/json或application/xml
     */
    const ACCEPT = 'application/json';

    /**
     * Send a short message.
     *
     * @param \Overtrue\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Overtrue\EasySms\Contracts\MessageInterface $message
     * @param \Overtrue\EasySms\Support\Config $config
     *
     * @return array
     *
     * @throws GatewayErrorException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $timestamp = date('YmdHis');
        $params = [
            'accountSid' => $config->get('accountSid'),
            'timestamp' => $timestamp,
            'respDataType' => 'JSON',
            'to' => !\is_null($to->getIDDCode()) ? strval($to->getZeroPrefixedNumber()) : $to->getNumber(),
            'smsContent' => $message->getContent($this),
        ];

        if (!is_null($message->getTemplate($this)) && is_array($message->getData($this))) {
            unset($params['smsContent']);
            $params['templateid'] = $message->getTemplate($this);
            $params['param'] =  implode(',',$message->getData($this));
        }
        $params['sig'] = $this->generateSign($params, $config->get('authToken'));

        $headers = [
            'Content-type:' . self::CONTENT_TYPE,
            'Accept: ' . self::ACCEPT
        ];

        $result = $this->post(self::BASE_URL,  $params, $headers);
        
        if ('00000' != $result['respCode']) {
            throw new GatewayErrorException($result['respDesc'], $result['respCode'], $result);
        }

        return $result;
    }

    /**
     * 生成秘钥
     * @param $params
     * @param $authToken
     * @return string
     */
    public function generateSign($params, $authToken)
    {
        return md5($params['accountSid'] . $authToken . $params['timestamp']);
    }
}
