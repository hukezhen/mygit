<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\common\controller\Common;
use app\common\model\Orders;
use app\common\model\ShopCar;
use app\common\model\Users as UsersModel;

/**
 * 前台公共控制器
 * @package app\index\controller
 */
class Home extends Common
{
    protected $data = [];

    protected $url = 'https://tana.xianzhou.cn/';

    protected $contentType = 'json';

    /**
     * 初始化方法
     */
    protected function initialize()
    {
        // 系统开关
        if (!config('web_site_status')) {
            $this->error('站点已经关闭，请稍后访问~');
        }

        //获取请求参数
        if ($this->request->isGet()) {
            $data = $this->request->get();
        } elseif ($this->request->isPost()) {
            $data = $this->request->post();
        } elseif ($this->request->isPut()) {
            $data = $this->request->put();
        } elseif ($this->request->isDelete()) {
            $data = $this->request->delete();
        } elseif ($this->request->isPatch()) {
            $data = $this->request->patch();
        } else {
            $data = $this->request->param();
        }
        $this->data = $data;

        $orderNumber = 0;
        $cartNumber = 0;
        if (!empty(session('users.uid'))) {
            $orderNumber = Orders::where('uid', session('users.uid'))->count();
            $cartNumber = ShopCar::where('uid', session('users.uid'))->count();
            $users = UsersModel::get(['uid' => session('users.uid')]);
        }
        $action = request()->action();
        $login = ['personal_data', 'personal_data_update', 'modify_address', 'address_update', 'address_create', 'cart', 'orders', 'payment'];
        if (empty(session('users.uid'))) {
            if (in_array($action, $login)) {
                $this->error("请先登录", '/login.html');
            }
        }
        $footer = false;
        $this->assign(compact('orderNumber', 'footer', 'cartNumber', 'users'));
    }


    /**
     * 成功返回
     * @param mixed $data
     * @param string $msg
     * @param int $code
     * @param array $header
     * @param mixed $type
     */
    protected function successResult($data, string $msg = '', int $code = 1, array $header = [], bool $type = false)
    {
        if (false === $type) {
            $type = $this->contentType;
        }
        $this->result($data, $code, $msg, $type, $header);
    }

    /**
     * 失败返回
     * @param string $msg
     * @param int $code
     * @param string $data
     * @param array $header
     * @param bool $type
     */
    protected function errorResult($msg = '', int $code = 0, $data = null, array $header = [], bool $type = false)
    {
        if (false === $type) {
            $type = $this->contentType;
        }
        $this->result($data, $code, $msg, $type, $header);
    }
}
