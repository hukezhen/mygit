<?php


namespace app\index\controller;


use app\admin\model\Attachment;
use app\common\model\Users as UsersModel;
use think\Db;

class Personal extends Home
{

    //个人资料
    public function index()
    {
        if ($this->request->isPost()) {
            $file = $this->request->file('file');
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move('uploads/face/');
            if ($info) {
                $model = UsersModel::update(['face' => $info->getPathname()], ['uid' => session('users.uid')]);
                if ($model) {
                    $this->successResult([], "头像修改成功");
                } else {
                    $this->errorResult("头像修改失败");
                }

            } else {
                // 上传失败获取错误信息
                $this->errorResult($file->getError());
            }
        }

//        $users = UsersModel::get(['uid' => session('users.uid')]);

//        $this->assign('users', $users);
        return $this->fetch();
    }

    /**
     * 资料修改
     * @return mixed|void
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            Db::startTrans();
            try {
                if (!empty($data['phone'])){
                    //验证联系电话
                    $isMob="/^1([3456789])\\d{9}$/";
                    $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
                    if(!preg_match($isMob,$data['phone']) && !preg_match($isTel,$data['phone'])){
                        throw new \Exception('联系电话格式错误');
                    }
                }
                $model = UsersModel::update($data, ['uid' => session('users.uid')]);
                if (!$model) {
                    throw new \Exception('修改失败');
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            return $this->successResult($model, '修改成功');
        }
        $face = Attachment::get(['id' => session(('users.face'))]);
//        $users = UsersModel::get(['uid' => session('users.uid')]);
        if ($face['path']) {
            $face_path = $this->url . $face['path'];
        } else {
            $face_path = '/static/home/img/tepy-logo.png';
        }

        $this->assign('face', $face_path);
//        $this->assign('users', $users);
        return $this->fetch();
    }

}