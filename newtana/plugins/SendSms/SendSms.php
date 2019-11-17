<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2018 Xianzhou Technology (Chongqing) LLC.
 */
namespace plugins\SendSms;


use app\common\controller\Plugin;

/**
 * Class 短信发送插件
 * @package plugins\Payment
 */
class SendSms extends Plugin{

    /**
     * @var array 插件信息
     */

    public $info = [
        // 插件名[必填]
        'name'        => 'SendSms',
        // 插件标题[必填]
        'title'       => '短信发送插件',
        // 插件唯一标识[必填],格式：插件名.开发者标识.plugin
        'identifier'  => 'send_sms.keacefull.plugin',
        // 插件图标[选填]
        'icon'        => 'fa fa-fw fa-info-circle',
        // 插件描述[选填]
        'description' => '通用短信发送插件,请先安装：overtrue/easy-sms,',
        // 插件作者[必填]
        'author'      => 'keacefull',
        // 作者主页[选填]
        'author_url'  => 'http://www.huangxiaomin.com',
        // 插件版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
        'version'     => '1.0.0',
        // 是否有后台管理功能[选填]
        'admin'       => '0',
    ];

    /**
     * 安装方法
     * @return bool
     */
    public function install(){
        if (!class_exists('Overtrue\EasySms\EasySms')){
            $this->error = '请先使用composer安装overtrue/easy-sms';
            return false;
        }

        return true;
    }

    /**
     * 卸载方法
     * @return bool
     */
    public function uninstall(){
        return true;
    }
}