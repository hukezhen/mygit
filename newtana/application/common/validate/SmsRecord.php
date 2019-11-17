<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\validate;


use think\Validate;

class SmsRecord extends Validate
{
    protected $rule = [
        'username' => ['require', 'alphaNum', 'unique' => 'users,username', 'length' => '4,16'],
        'password' => ['require', 'length' => '6,16'],
        'telephone' => ['require', 'unique' => 'users,telephone', 'length' => 11, 'regex' => '/^1([3456789])\\d{9}$/'],
        'scene' => ['require', 'in' => '8,9,10,11,12,13,14'],
    ];

    protected $field = [
        'telephone' => '手机号码',
        'scene' => '验证码场景'
    ];

    protected $message = [
        'username.require' => '用户账号不能为空！',
        'username.length' => '用户账号长度为4-16位!',
        'username.unique' => '用户账号已存在!',
        'username.alphaNum' => '登录名称只能是字母或数字的组合!',
        'telephone.require' => '手机号码不能为空！',
        'telephone.unique' => '手机号码已存在！',
        'telephone.length' => '手机号码的长度必须为11位！',
        'telephone.regex' => '手机号码格式错误！',
        'scene.require' => '验证码场景不能为空!',
        'scene.in' => '非法的验证码场景!',
        'password.require' => '登录密码不能为空!',
        'password.length' => '登录密码长度为6-16位!',

    ];

    /**
     * 找回密码验证场景
     * @return SmsRecord
     */
    public function sceneForget(){
        return $this->only(['telephone','scene'])
            ->remove('telephone','unique');
    }

}