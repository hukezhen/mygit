<?php


namespace app\index\controller;


use app\common\model\SmsRecord;
use app\common\model\Users as UsersModel;
use think\Db;

class Auth extends Home
{

    public function login()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (empty($data['username']) || empty($data['password'])) {
                $this->error('用户名或者密码不能为空');
            }

            $param = [
                [
                    ['username', '=', $data['username']],
                ],
                [
                    ['telephone', '=', $data['username']],
                ],
            ];

            $users = UsersModel::whereOr($param)->find();
//            $this->successResult($users);

            if (!$users || !$users->verifyPassword($data['password'])) {
                $this->error('用户名或者密码错误！');
            }
            if ($users['status'] != 1) {
                $this->error('您的账号已被封停！');
            }
            session('users', $users);
            $this->success();
        }

        return $this->fetch();
    }

    /**
     * 注册
     * @return mixed
     * @throws \Exception
     */
    public function register()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!isset($data['type'])) {
                $data['type'] = 0;
            }

            if (!empty($data['qusername']) || !empty($data['gusername'])) {
                $data['username'] = !empty($data['qusername']) ? $data['qusername'] : $data['gusername'];
            }

            $result = $this->validate($data, 'Users');
            if (true !== $result) $this->error($result);

            //判断密码是否一致
            if ($data['password'] !== $data['oldPassword']) {
                $this->error("两次密码输入不一致");
            }

//            //短信验证码
            $sms = SmsRecord::checkCodeByTelephone($data['telephone'], 8, $data['sms_code']);
            if ($sms !== true) {
                $this->error($sms);
            }
//
            //captcha_check
//            if (!captcha_check($data['code'])) {
//                //验证失败
//                $this->error('验证码错误或失效');
//            }


            Db::startTrans();
            try {
                $model = UsersModel::create($data);
                if (false === $model) {
                    $this->error('注册失败！');
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->errorResult($e->getMessage());
            }
            session('users', $model);
            $this->success("注册成功", '/index.html');
        }

        return $this->fetch();
    }

    /**
     * @throws \Exception
     */
    public function web_register()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['type'] = 0;

            $result = $this->validate($data, 'Users');
            if (true !== $result) $this->error($result);

            //判断密码是否一致
            if ($data['password'] !== $data['oldPassword']) {
                $this->error("两次密码输入不一致");
            }

//            //短信验证码
            $sms = SmsRecord::checkCodeByTelephone($data['telephone'], 8, $data['sms_code']);
            if ($sms !== true) {
                $this->error($sms);
            }
//
            //captcha_check
//            if (!captcha_check($data['code'], '')) {
//                //验证失败
//                $this->error('验证码错误或失效');
//            };


            Db::startTrans();
            try {
                $model = UsersModel::create($data);
                if (false === $model) {
                    $this->error('注册失败！');
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->errorResult($e->getMessage());
            }
            session('users', $model);
            $this->success("注册成功");
        }
    }

    /**
     * 找回密码
     * @return mixed
     * @throws \Exception
     */
    public function retrieve()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //判断密码是否一致
            if ($data['password'] !== $data['oldPassword']) {
                $this->error("两次密码输入不一致");
            }

            $model = UsersModel::where('telephone', $data['telephone'])->find();
            if (!$model) {
                $this->errorResult("该用户不存在");
            }

            //短信验证码
            $sms = SmsRecord::checkCodeByTelephone($data['telephone'], 11, $data['code']);
            if ($sms !== true) {
                $this->error($sms);
            }

            $model->password = $data['password'];
            if ($model->save()) {
                $this->successResult([], "修改成功");
            } else {
                $this->errorResult("修改失败");
            }
        }
        return $this->fetch();
    }


}