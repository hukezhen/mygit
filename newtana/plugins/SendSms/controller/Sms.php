<?php

/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2018 Xianzhou Technology (Chongqing) LLC.
 */

namespace plugins\SendSms\controller;


use app\common\controller\Common;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Overtrue\EasySms\PhoneNumber;
use plugins\SendSms\gateway\IpaynowGateway;
use plugins\SendSms\gateway\MiaodiyunGateway;
use plugins\SendSms\gateway\QiruiGateway;
use plugins\SendSms\gateway\SmsbaoGateway;
use Overtrue\EasySms\Gateways\ChuanglanGateway;
use plugins\SendSms\gateway\VeesingGateway;


class Sms extends Common
{
    /**
     * @param $data
     *
     * @return array|bool
     *
     * @throws \Exception
     */
    public function send($data)
    {
        $pluginsConfig = plugin_config('SendSms');

        $smsGateways = $pluginsConfig['gateways'];

        $gateways = [];

        //读取配置文件
        for ($i = 0; $i < count($smsGateways); ++$i) {
            switch ($smsGateways[$i]) {
                case 'aliyun': //阿里云
                    $gateways['aliyun'] = [
                        'access_key_id' => $pluginsConfig['aliyun_access_key_id'],
                        'access_key_secret' => $pluginsConfig['aliyun_access_key_secret'],
                        'sign_name' => $pluginsConfig['aliyun_sign_name'],
                    ];
                    break;
                case 'aliyunrest': //阿里云Rest
                    $gateways['aliyunrest'] = [
                        'app_key' => $pluginsConfig['aliyunrest_app_key'],
                        'app_secret_key' => $pluginsConfig['aliyunrest_app_secret_key'],
                        'sign_name' => $pluginsConfig['aliyunrest_sign_name'],
                    ];
                    break;
                case 'yunpian': //云片
                    $gateways['yunpian'] = [
                        'api_key' => $pluginsConfig['yunpian_api_key'],
                        'signature' => $pluginsConfig['yunpian_signature'],
                    ];
                    break;
                case 'submail':
                    $gateways['submail'] = [
                        'app_id' => $pluginsConfig['submail_app_id'],
                        'app_key' => $pluginsConfig['submail_app_key'],
                        'project' => $pluginsConfig['submail_project'],
                    ];
                    break;
                case 'luosimao':
                    $gateways['luosimao'] = [
                        'api_key' => $pluginsConfig['luosimao_api_key'],
                    ];
                    break;
                case 'yuntongxun':
                    $gateways['yuntongxun'] = [
                        'app_id' => $pluginsConfig['yuntongxun_api_id'],
                        'account_sid' => $pluginsConfig['yuntongxun_account_sid'],
                        'account_token' => $pluginsConfig['yuntongxun_account_token'],
                        'is_sub_account' => $pluginsConfig['yuntongxun_is_sub_account'],
                    ];
                    break;
                case 'huyi':
                    $gateways['huyi'] = [
                        'app_id' => $pluginsConfig['huyi_api_id'],
                        'api_key' => $pluginsConfig['huyi_api_key'],
                        'signature' => $pluginsConfig['huyi_signature'],
                    ];
                    break;
                case 'juhe':
                    $gateways['juhe'] = [
                        'app_key' => $pluginsConfig['juhe_app_key'],
                    ];
                    break;

                case 'sendcloud':
                    $gateways['sendcloud'] = [
                        'sms_user' => $pluginsConfig['sendcloud_sms_user'],
                        'sms_key' => $pluginsConfig['sendcloud_sms_key'],
                        'timestamp' => $pluginsConfig['sendcloud_timestamp'],
                    ];
                    break;

                case 'baidu':
                    $gateways['baidu'] = [
                        'ak' => $pluginsConfig['baidu_ak'],
                        'sk' => $pluginsConfig['baidu_sk'],
                        'invoke_id' => $pluginsConfig['baidu_invoke_id'],
                        'domain' => $pluginsConfig['baidu_domain'],
                    ];
                    break;
                case 'huaxin':
                    $gateways['huaxin'] = [
                        'user_id' => $pluginsConfig['huaxin_user_id'],
                        'password' => $pluginsConfig['huaxin_password'],
                        'account' => $pluginsConfig['huaxin_account'],
                        'ip' => $pluginsConfig['huaxin_ip'],
                        'ext_no' => $pluginsConfig['huaxin_ext_no'],
                    ];
                    break;
                case 'chuanglan':
                    $gateways['chuanglan'] = [
                        'account' => $pluginsConfig['chuanglan_account'],
                        'password' => $pluginsConfig['chuanglan_apssword'],
                        'channel' => $pluginsConfig['chuanglan_channel'] == 0 ? ChuanglanGateway::CHANNEL_VALIDATE_CODE : ChuanglanGateway::CHANNEL_PROMOTION_CODE,
                        // 会员营销通道 特定参数。创蓝规定：api提交营销短信的时候，需要自己加短信的签名及退订信息
                        'sign' => $pluginsConfig['chuanglan_sign'],
                        'unsubscribe' => $pluginsConfig['chuanglan_unsubscribe'],
                    ];
                    break;
                case 'rongcloud':
                    $gateways['rongcloud'] = [
                        'app_key' => $pluginsConfig['rongcloud_app_key'],
                        'app_secret' => $pluginsConfig['rongcloud_app_secret'],
                    ];
                    break;

                case 'tianyiwuxian':
                    $gateways['tianyiwuxian'] = [
                        'username' => $pluginsConfig['tianyiwuxian_username'],
                        'password' => $pluginsConfig['tianyiwuxian_passwordy'],
                        'gwid' => $pluginsConfig['tianyiwuxian_gwid'],
                    ];
                    break;

                case 'twilio':
                    $gateways['twilio'] = [
                        'account_sid' => $pluginsConfig['tianyiwuxian_account_sid'],
                        'from' => $pluginsConfig['tianyiwuxian_from'],
                        'token' => $pluginsConfig['tianyiwuxian_token'],
                    ];
                    break;

                case 'qcloud':
                    $gateways['qcloud'] = [
                        'sdk_app_id' => $pluginsConfig['qcloud_sdk_app_id'],
                        'app_key' => $pluginsConfig['qcloud_app_key'],
                        'sign_name' => $pluginsConfig['qcloud_sign_name'],
                    ];
                    break;
                case 'avatardata':
                    $gateways['avatardata'] = [
                        'app_key' => $pluginsConfig['avatardata_app_key'],
                    ];
                    break;
                case'huawei':
                    $gateways['huawei'] = [
                        'endpoint' => $pluginsConfig['avatardata_app_key'],
                        'app_key' => $pluginsConfig['hauwei_app_key'],
                        'app_secret' => $pluginsConfig['hauwei_app_secret'],
                        'callback' => $pluginsConfig['hauwei_callback'],
                    ];
                    break;
                case'miaodiyun':
                    $gateways['miaodiyun'] = [
                        'accountSid' => $pluginsConfig['miaodiyun_accountSid'],
                        'authToken' => $pluginsConfig['miaodiyun_authToken'],
                    ];
                    break;
                case'qirui':
                    $gateways['qirui'] = [
                        'username' => $pluginsConfig['qirui_username'],
                        'password' => $pluginsConfig['qirui_password'],
                    ];
                    break;
                case'smsbao':
                    $gateways['smsbao'] = [
                        'username' => $pluginsConfig['smsbao_username'],
                        'password' => $pluginsConfig['smsbao_password'],
                    ];
                    break;
                case'veesing':
                    $gateways['veesing'] = [
                        'account' => $pluginsConfig['veesing_account'],
                        'password' => $pluginsConfig['veesing_password'],
                    ];
                    break;
                case'ipaynow':
                    $gateways['ipaynow'] = [
                        'appId' => $pluginsConfig['ipaynow_appId'],
                        'md5' => $pluginsConfig['ipaynow_md5'],
                        'des_key' => $pluginsConfig['ipaynow_3des'],
                        'notifyUrl' => $pluginsConfig['ipaynow_notifyUrl'],
                    ];
                    break;
            }
        }

        //配置文件
        $config = [
            'default' => [
                'gateways' => $smsGateways,
            ],
            'gateways' => $gateways,
        ];
        $easySms = new EasySms($config);
        //注册秒滴云
        $easySms->extend('miaodiyun', function ($gatewayConfig) {
            return new MiaodiyunGateway($gatewayConfig);
        });
        //注册启端云
        $easySms->extend('qirui', function ($gatewayConfig) {
            return new QiruiGateway($gatewayConfig);
        });
        //注册短信宝
        $easySms->extend('smsbao', function ($gatewayConfig) {
            return new SmsbaoGateway($gatewayConfig);
        });
        //注册中昱维信
        $easySms->extend('veesing', function ($gatewayConfig) {
            return new VeesingGateway($gatewayConfig);
        });
        //注册soso短信 https://soso.ipaynow.cn/
        $easySms->extend('ipaynow', function ($gatewayConfig) {
            return new IpaynowGateway($gatewayConfig);
        });
        try {
            $content = [];
            !empty($data['content']) && $content['content'] = $data['content'];
            !empty($data['data']) && $content['data'] = $data['data'];
            !empty($data['template']) && $content['template'] = $data['template'];
            empty($data['nationCode']) && $data['nationCode'] = 86;
            $telephone = new PhoneNumber($data['telephone'], $data['nationCode']);

            $result = $easySms->send($telephone, $content);
        } catch (NoGatewayAvailableException $e) {
            return false;
        }
        return true;
    }
}
