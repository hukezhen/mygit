<?php

/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2018 Xianzhou Technology (Chongqing) LLC.
 */

//可用短信渠道
$gateways = [
//    'aliyun' => '阿里云',
//    'aliyunrest' => '阿里云 Rest',
//    'yunpian' => '云片',
//    'submail' => 'Submail',
//    'luosimao' => '螺丝帽',
//    'yuntongxun' => '容联云通讯',
//    'huyi' => '互亿无线',
//    'juhe' => '聚合数据',
//    'sendcloud' => 'SendCloud',
//    'baidu' => '百度云',
//    'huaxin' => '华信短信平台',
//    'chuanglan' => '253云通讯（创蓝）',
//    'rongcloud' => '融云',
//    'tianyiwuxian' => '天毅无线',
//    'twilio' => 'twilio',
//    'qcloud' => '腾讯云 SMS',
//    'veesing' => '中昱维信',
//    'avatardata' => '阿凡达数据',
//    'huawei' => '华为云',
//    'miaodiyun' => '秒滴云',
//    'qirui' => '启端云',
//    'smsbao' => '短信宝',
    'ipaynow' => '嗖嗖短信',
];

return [
    ['group',
        [
            '公共配置' => [
                ['checkbox', 'gateways', '短信发送渠道', '', $gateways, ['ipaynow']],
                ['number', 'timeout', 'HTTP 请求的超时时间（秒）', '', '5.0'],
            ],
//            '阿里云' => [
//                ['text', 'aliyun_access_key_id', '阿里云的ID'],
//                ['text', 'aliyun_access_key_secret', '阿里云的Secret'],
//                ['text', 'aliyun_sign_name', '阿里云的短信签名'],
//            ],
//            '阿里云 Rest' => [
//                ['text', 'aliyunrest_app_key', 'app_key'],
//                ['text', 'aliyunrest_app_secret_key', 'app_secret_key'],
//                ['text', 'aliyunrest_sign_name', 'sign_name'],
//            ],
//            '云片' => [
//                ['text', 'yunpian_api_key', 'APP_KEY'],
//                ['text', 'yunpian_signature', '签名'],
//            ],
//            'Submail' => [
//                ['text', 'submail_app_id', 'APP_ID'],
//                ['text', 'submail_app_key', 'APP_KEY'],
//                ['text', 'submail_project', 'project'],
//            ],
//            '螺丝帽' => [
//                ['text', 'luosimao_api_key', 'API_KEY'],
//            ],
//            '容联云通讯' => [
//                ['text', 'yuntongxun_api_id', 'app_id'],
//                ['text', 'yuntongxun_account_sid', 'account_sid'],
//                ['text', 'yuntongxun_account_token', 'account_token'],
//                ['text', 'yuntongxun_is_sub_account', 'is_sub_account'],
//            ],
//            '互亿无线' => [
//                ['text', 'huyi_api_id', 'API_ID'],
//                ['text', 'huyi_api_key', 'API_KEY'],
//                ['text', 'huyi_signature', '签名'],
//            ],
//            '聚合数据' => [
//                ['text', 'juhe_app_key', 'app_key'],
//            ],
//            'SendCloud' => [
//                ['text', 'sendcloud_sms_user', 'sms_user'],
//                ['text', 'sendcloud_sms_key', 'sms_key'],
//                ['select', 'sendcloud_timestamp', 'timestamp', '', ['true' => true, 'false' => false]],
//            ],
//            '百度云' => [
//                ['text', 'baidu_ak', 'ak'],
//                ['text', 'baidu_sk', 'sk'],
//                ['text', 'baidu_invoke_id', 'invoke_id'],
//                ['text', 'baidu_domain', 'domain'],
//            ],
//            '华信短信平台' => [
//                ['text', 'huaxin_user_id', 'user_id'],
//                ['text', 'huaxin_password', 'password'],
//                ['text', 'huaxin_account', 'account'],
//                ['text', 'huaxin_ip', 'ip'],
//                ['text', 'huaxin_ext_no', 'ext_no'],
//            ],
//            '253云通讯（创蓝）' => [
//                ['text', 'chuanglan_account', '账号'],
//                ['text', 'chuanglan_apssword', '密码'],
//                ['radio', 'chuanglan_channel', '短信发送通道', '', ['0' => '验证码通道', '1' => '营销短信通道']],
//                ['text', 'chuanglan_sign', '签名'],
//                ['text', 'chuanglan_unsubscribe', '退订信息', '参数可选'],
//            ],
//            '融云' => [
//                ['text', 'rongcloud_app_key', 'app_key'],
//                ['text', 'rongcloud_app_secret', 'app_secret'],
//            ],
//            '天毅无线' => [
//                ['text', 'tianyiwuxian_username', 'username'],
//                ['text', 'tianyiwuxian_password', 'password'],
//                ['text', 'tianyiwuxian_gwid', 'gwid'],
//            ],
//            'twilio' => [
//                ['text', 'tianyiwuxian_account_sid', 'account_sid'],
//                ['text', 'tianyiwuxian_from', 'from'],
//                ['text', 'tianyiwuxian_token', 'token'],
//            ],
//            '腾讯云' => [
//                ['text', 'qcloud_sdk_app_id', 'APP_ID'],
//                ['text', 'qcloud_app_key', 'APP_KEY'],
//                ['text', 'qcloud_sign_name', '签名'],
//            ],
//            '阿凡达数据' => [
//                ['text', 'avatardata_app_key', 'app_key'],
//            ],
//            '华为云 SMS' => [
//                ['text', 'hauwei_endpoint', 'APP接入地址'],
//                ['text', 'hauwei_app_key', 'app_key'],
//                ['text', 'hauwei_app_secret', 'app_secret'],
//                ['text', 'hauwei_callback', 'callback'],
//            ],
//            '秒滴云' => [
//                ['text', 'miaodiyun_accountSid', 'accountSid'],
//                ['text', 'miaodiyun_authToken', 'authToken'],
//            ],
//            '启端云' => [
//                ['text', 'qirui_username', 'username'],
//                ['text', 'qirui_password', 'password'],
//            ],
//            '短信宝' => [
//                ['text', 'smsbao_username', 'username'],
//                ['text', 'smsbao_password', 'password'],
//            ],
//            '中昱维信' => [
//                ['text', 'veesing_account', 'account'],
//                ['text', 'veesing_password', 'password'],
//            ],
            '嗖嗖短信' => [
                ['text', 'ipaynow_appId', 'appId'],
                ['text', 'ipaynow_md5', 'MD5密钥：'],
                ['text', 'ipaynow_3des', '3DES密钥：'],
                ['text', 'ipaynow_notifyUrl', 'notifyUrl', '', request()->domain()],
            ]
        ],
    ],
];
