<?php


namespace app\index\controller;


use app\common\model\Region;
use app\common\model\UsersAddress;
use think\Db;

class Address extends Home
{
    /**
     * 地址列表
     * @return mixed
     * @throws \Exception
     */
    public function index($mode = 0)
    {
        $url = $this->request->server('HTTP_REFERER');
        if ($mode == 0 && !empty($url)) {
            session('settlement_url', $url);
        }
        $model = UsersAddress::where(['uid' => session('users.uid'), 'status' => 1])->select();
        $this->assign(compact('model', 'mode'));
        return $this->fetch();
    }

    /**
     * 地址添加
     * @return mixed|void
     */
    public function save()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();

            if (!preg_match("/^1[3456789]{1}\d{9}$/", $data['postal_code'])) {
                $this->errorResult("联系电话不符合要求");
            }

            $data['uid'] = session('users.uid');
            $result = $this->validate($data, 'app\common\validate\UsersAddress');
            if ($result !== true) $this->error($result);

            Db::startTrans();
            try {
                $model = UsersAddress::create($data, true);
                if (!$model) {
                    return $this->errorResult('添加失败');
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            return $this->successResult($model, '添加成功');
        }

        return $this->fetch();
    }

    /**
     * 地址修改
     * @param $id
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function update($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            Db::startTrans();
            try {
                if (!preg_match("/^1[3456789]{1}\d{9}$/", $data['postal_code'])) {
                    throw new \Exception("联系电话不符合要求");
                }
                $data['id'] = $id;
                $data['uid'] = session('users.uid');
                $result = $this->validate($data, 'app\common\validate\UsersAddress');
                if (true !== $result) {
                    throw new \Exception($result);
                }
                $model = UsersAddress::update($data, true);
                if (!$model) {
                    throw new \Exception('修改失败');
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->errorResult($e->getMessage());
            }
            return $this->successResult($model, '修改成功');
        }

        $model = UsersAddress::where(['uid' => session('users.uid'), 'id' => $id])->find();
        if (!$model) {
            $this->error("参数传递错误");
        }

        $this->assign('model', $model);
        return $this->fetch();
    }

    /**
     * 地址删除
     * @throws \Exception
     */
    public function delete()
    {
        $data = $this->request->get();

        $model = UsersAddress::where(['id' => $data['id'], 'uid' => session('users.uid')])->find();
        if (!$model) {
            $this->errorResult("参数传递错误");
        }

        $model->status = 0;
        if ($model->save()) {
            $this->successResult([], '删除成功');
        } else {
            $this->errorResult("删除失败");
        }
    }

    /**
     * 默认地址
     * @throws \Exception
     */
    public function default()
    {
        $data = $this->request->get();

        $model = UsersAddress::where(['id' => $data['id'], 'uid' => session('users.uid')])->find();
        if (!$model) {
            $this->errorResult("参数传递错误");
        }

        $address = UsersAddress::where(['uid' => session('users.uid'), 'default' => 1])->find();
        if ($address) {
            $address->default = 0;
            if (!$address->save()) {
                $this->errorResult('设置失败');
            }
        }

        $model->default = 1;
        if ($model->save()) {
            $this->successResult([], '设置成功');
        } else {
            $this->errorResult("设置失败");
        }
    }

    /**
     * 地区联动
     * @throws \Exception
     */
    public function region()
    {
        if ($this->request->isGet()) {
            $data = $this->request->get();
            if (!isset($data['pid'])) {
                $data['pid'] = 0;
            }
            $model = Region::where(['pid' => $data['pid']])->field('id,name,abbr,level,pid,code')->select();

            return $this->successResult($model);
        }
    }

}