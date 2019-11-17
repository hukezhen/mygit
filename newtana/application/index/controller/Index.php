<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\admin\model\Attachment;
use app\common\model\Carousel;
use app\common\model\Craft;
use app\common\model\CraftType;
use app\common\model\EmailSubscribe;
use app\common\model\Gallery;
use app\common\model\GalleryType;
use app\common\model\Orders;
use app\common\model\Product;
use app\common\model\ProductType;
use app\common\model\Region;
use app\common\model\ShopCar;
use app\common\model\SmsRecord;
use app\common\model\Users;
use app\common\model\Users as UsersModel;
use app\common\model\UsersAddress;
use think\Db;
use think\facade\Env;
use think\facade\Log;

/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Home
{

    public function email()
    {
        $email = $this->request->post('email', '');
        if (empty($email)) {
            $this->error('邮件地址不能为空！');
        }

        if (EmailSubscribe::get(['email' => $email])) {
            $this->error('该邮箱已经订阅！');
        }


        $EmailSubscribe = EmailSubscribe::create([
            'ip' => $this->request->ip(),
            'email' => $email
        ]);

        if (false === $EmailSubscribe) {
            $this->error('订阅失败！');
        }

        $this->error('订阅成功！');

    }

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        if (config('home_default_module') != 'index') {
            $this->redirect(config('home_default_module') . '/index/index');
        }
        //顶部轮播
        $carousel = Carousel::where('status', 1)->where('type', 1)->order('sort', 'asc')->select();
        //畅销爆款
        $sellProduct = Product::where('status', 1)->where('recommend', 2)->order('sort', 'asc')->select();
        //新品
        $newProduct = Product::where('recommend', 1)->where('status', 1)->order('sort', 'asc')->select();
        $footer = true;
        $this->assign(compact('carousel', 'sellProduct', 'newProduct', 'footer'));
        return $this->fetch();
    }

    /**
     * 全系轮毂
     * @param int $type
     * @return mixed
     * @throws \Exception
     */
    public function hub($type = 0, $vehicle = 0)
    {

        $productType = ProductType::where('type', 1)->where('status', 1)->select();
        $vehicleType = ProductType::where('type', 3)->where('status', 1)->select();


        //select u.id,u.名称，group_count(b.名称) from u left join b on find_in_set(b.id,u.爱好Id) group by u.id;


        $query = Product::where('a.status', 1)->alias('a');

        if ($type != 0) {
            $query->where('a.series', $type);
        }
        if ($vehicle != 0) {
            $query->where('a.vehicle_type', $vehicle);
        }

        $query->field('a.*,group_concat(b.title) as molding_process_text');
        $query->leftJoin('product_type b', 'find_in_set(b.id,a.molding_process)');
        $query->group('a.id');
        $query->where('a.mode',1);
        $query->order('a.sort', 'asc');
        $product = $query->select();

        $this->assign(compact('product', 'type', 'productType', 'vehicleType', 'vehicle'));
        return $this->fetch();
    }

    /**
     * 轮毂详情
     * @return mixed
     */
    public function detail($id)
    {

        $query = Product::where('a.status', 1)->alias('a');
        $query->field(['a.*',
            'group_concat(DISTINCT b.title) as molding_process_text',
            'group_concat(DISTINCT bb.title) as surface_state_text',
            'c.title as series_text',
            'c.title_en as series_en_text',
            'd.title as brand_text',
            'e.title as vehicle_type_text',
            'f.title as motorcycle_type_text',
        ]);
        $query->leftJoin('product_type b', 'find_in_set(b.id,a.molding_process)');
        $query->leftJoin('product_type bb', 'find_in_set(bb.id,a.surface_state)');
        $query->leftJoin('product_type c', 'c.id = a.series');
        $query->leftJoin('product_type d', 'd.id = a.brand');
        $query->leftJoin('product_type e', 'e.id = a.vehicle_type');
        $query->leftJoin('product_type f', 'f.id = a.motorcycle_type ');
        $query->group('a.id');
        $query->where('a.id', $id);
        $query->where('a.mode', 1);

        $product = $query->find();

        if ($this->request->isPost()) {
            $number = $this->request->post('number', 1);

            $orders = Orders::create([
                'uid' => session('users.uid'),
                'pid' => $id,
                'number' => $number,
                'moneys' => bcmul($number, $product['price'], 2)
            ]);

            if (false === $orders) {
                $this->error('购买失败');
            }

            $this->success('下单成功', '', ['id' => $orders['id']]);
        }


        $this->assign(compact('product'));

        return $this->fetch();
    }

    /**
     * 潮流画廊
     * @return mixed
     * @throws \Exception
     */
    public function gallery($id = 0, $pid = 0)
    {
        if ($this->request->isPost()) {
            $brand = $this->request->post('brand', 0);
            if ($brand != 0) {
                $list = GalleryType::where('status', 1)->field('id,title')->where('pid', $brand)->select();
                $this->success('', '', $list);
            }
        }
        $galleryType = GalleryType::where('status', 1)->field('id,title')->where('pid', 0)->select();
        $query = Gallery::where('a.status', 1)->field('a.id,a.cover')->alias('a')
            ->leftJoin('gallery_type b', 'a.types_id=b.id');

        if ($id != 0) {
            $query->where('a.types_id', $id);
        }
        if ($pid != 0) {
            $query->where('b.pid', $pid);
        }

        $gallery = $query->select();
        $this->assign(compact('galleryType', 'gallery', 'id', 'pid'));
        return $this->fetch();
    }

    /**
     * @param int $id
     */
    public function gallery_detail($id)
    {
        $gallery = Gallery::get($id);

        $query = Product::where('a.status', 1)->alias('a');
        $query->field(['a.*',
            'group_concat(b.title) as molding_process_text',
        ]);
        $query->leftJoin('product_type b', 'find_in_set(b.id,a.molding_process)');
        $query->group('a.id');
        $query->where('a.id', 'in', $gallery['pid']);

        $product = $query->select();
        $this->assign(compact('gallery', 'product'));
        return $this->fetch();
    }

    /**
     * 工艺大师
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function craft($id = 0)
    {
        $craftType = CraftType::where('status', 1)->order('sort', 'asc')->select();
        $query = Craft::where('status', 1)->order('sort', 'asc');

        if ($id != 0) {
            $query->where('types_id', $id);
        }
        $craft = $query->select();
        $this->assign(compact('craftType', 'craft', 'id'));
        return $this->fetch();
    }

    public function craft_detail($id)
    {

        $craft = Craft::get($id);
        $this->assign(compact('craft', 'id'));
        return $this->fetch();
    }

    /**
     * 联系我们
     * @return mixed
     */
    public function contact()
    {
        return $this->fetch();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session('users', null);
        $this->redirect('/');
    }

    /**
     * git代码自动提交
     */
    public function git()
    {
        error_reporting(1);
        $target = '/www/wwwroot/xianzhou/tana';
        $token = '7fInONNieATG4yeKfv';
        if (empty($_SERVER['HTTP_X_GITLAB_TOKEN']) || $_SERVER['HTTP_X_GITLAB_TOKEN'] !== $token) {
            exit('error request');
        }
        $output = shell_exec("cd $target && sudo /usr/local/git/bin/git pull 2>&1");
        Log::info(" output:" . json_encode($output));
    }
}
