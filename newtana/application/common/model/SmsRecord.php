<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\model;

use think\Model;


/**
 * Class SmsRecord 短信发送记录
 * @package app\common\model
 * @property string $telephone 手机号码
 * @property string $code 短信验证码
 * @property string $scene 发送场景
 * @property string $expires 验证码有效期(单位分钟,默认5分钟)
 * @property string $nation_code 国际码(默认86)
 * @property string $status 状态（默认1）
 * @property string $create_time 创建时间
 * @property string $update_time 最后更新时间
 */
class SmsRecord extends Model
{

    public $autoWriteTimestamp = true;


    /**
     * @param int $value
     *
     * @return mixed
     */
    public static function getSceneData($value, $status = 2)
    {
        $scene = [
            0 => '测试',
            8 => '绑定',
            9 => '登录',
            10 => '注册',
            11 => '找回密码',
            12 => '提现',
            13 => '安全校验'
        ];
        return $scene[$value] ?? $scene[0];
    }

    /**
     *判断是否需要登录
     *
     * @param $scene
     * @param $user
     *
     * @return bool
     */
    public static function validateIsToken($scene, $user)
    {
        if ($scene > 12 && empty($user)) {
            return false;
        }
        return true;
    }


    /**
     *
     * 检查验证码是否正确
     *
     * @param $telephone
     * @param $scene
     * @param $sms_code
     *
     * @return bool|string
     * @throws \Exception
     */
    public static function checkCodeByTelephone($telephone, $scene, $sms_code)
    {
        $sms = self::where('telephone', $telephone)
            ->where('scene', $scene)
            ->where('status', 1)
            ->order('create_time', 'desc')
            ->find();
        if (!$sms) {
            return '还没有发送验证码！';
        }
//        if ($sms['expires'] * 60 + $sms['create_time'] < time()) {
//            return '验证码已经过期！';
//        }

        if ($sms_code != $sms['code']) {
            return '验证码错误！';
        }

        return true;
    }

}