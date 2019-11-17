<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\index\controller;

use app\common\validate\SmsRecord as SmsRecordValidate;
use app\common\model\SmsRecord as SmsRecordModel;
use app\common\model\Users as UsersModel;
use think\Db;


class Sms extends Home
{

    protected $isToken = false;

    /**
     * 重新发送 ~~ 发送一条验证码.
     */
    public function save()
    {
        if(isset($this->data['code'])){
            if (!captcha_check($this->data['code'], '')) {
                //验证失败
                $this->errorResult('图形码错误');
            }
        }

        Db::startTrans();
        try {
            //第一步验证流程
            $status = 2;
            $validate = new SmsRecordValidate();
            $this->data['scene'] == 11 && $validate->scene('forget');
            $this->data['nation_code'] = '86';
            if (!$validate->check($this->data)) {
                throw new \Exception($validate->getError());
            }
            //验证是否需要登录
            if (false === SmsRecordModel::validateIsToken($this->data['scene'], session('users'))) {
                throw new \Exception('该操作需要登录');
            }
            //检测用户是否可以注册
            $user = UsersModel::get(['telephone' => $this->data['telephone']]);
            if ($this->data['scene'] == 11 && !$user){
                throw new \Exception('手机号码还没有注册!');
            }
            if ($user && $user->getData('status') == 0) {
                throw new \Exception('用户被禁止登录!');
            }
            if ($user && $user->getData('status') == 2) {
                throw new \Exception('您的账号已申请注销！');
            }
            //检测手机号码是否已经注册
            if ($this->data['scene'] == 10 && $user) {
                throw new \Exception('手机号码已被注册！');
            }
            $this->data['scene'] == 8 && $status = 1;
            (!$user && $this->data['scene'] == 8) && $status = 0;
            //执行发送操作
            $code = rand(1000, 9999);

            $res = sendSmsCode($this->data['telephone'], $this->data['scene'], $code, $status,'5', $this->data['nation_code']);
            if (!is_array($res)) {
                throw new \Exception($res);
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->errorResult($e->getMessage());
        }

        $this->successResult(['id' => $res['id'], 'status' => $status]);
    }

    /**
     * @param $id
     *
     * @throws \Exception
     */
    public function update()
    {
        if (empty($this->data['code'])) {
            $this->errorResult('验证码不能为空!');
        }
        if (empty($this->data['telephone'])) {
            $this->errorResult('手机号码不能为空!');
        }
        if (empty($this->data['scene'])) {
            $this->errorResult('验证场景不能为空!');
        }

        $check = SmsRecordModel::checkCodeByTelephone($this->data['telephone'], $this->data['scene'], $this->data['code']);

        if (true === $check) {
            $this->successResult([], '验证码正确!');
        }
        $this->errorResult($check);
    }

    /**
     * 获取验证码状态.
     *
     * @param int $id
     *
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        $res = SmsRecordModel::get($id);
        if (!$res) {
            $this->errorResult('验证码不存在!');
        }
        $data = $res->getData();
        unset($data['code']);
        unset($data['update_time']);
        $this->successResult($data);
    }


    /**
     * 使一条验证码立即失效.
     *
     * @param int $id
     *
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $res = SmsRecordModel::get($id);
        if (!$res) {
            $this->errorResult('验证码不存在');
        }
        if (false === $res->data(['state' => 40])->isUpdate(true)->save()) {
            $this->errorResult('操作失败');
        }
        $this->successResult(['status' => true]);
    }
}