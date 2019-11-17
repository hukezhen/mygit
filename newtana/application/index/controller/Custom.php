<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\index\controller;

use app\common\model\Custom as CustomModel;
use app\common\model\Orders;
use app\common\model\Orders as OrdersModel;
use app\common\model\OrdersDetail;
use app\common\model\Product;
use app\common\model\ProductType as ProductTypeModel;
use app\common\model\ShopCar;
use app\common\model\UsersAddress;
use think\Db;

class Custom extends Home
{
    /**
     * 个性定制首页
     * @return mixed
     */
    public function index()
    {
        $map = [
            ['status', '=', 1],
            ['mode', '=', 2]
        ];

        $model = Product::where($map)->select();

        $this->assign('model', $model);
        return $this->fetch();
    }

    public function step1($id)
    {
        $model = Product::where(['status' => 1, 'id' => $id])->find();
//        dump($model);exit;
        if (!$model) {
            $this->error("该商品已下架");
        }

        $list = Product::where('status', 1)->where('id', '<>', $model['id'])->where('mode', 2)->select();
//        dump($list);

        $banner = ProductTypeModel::where(['type' => 2, 'status' => 1])->field('id,title,type')->select();

        $mode = ProductTypeModel::where(['type' => 3, 'status' => 1])->field('id,title,type')->select();
//        dump($list);exit;
        $this->assign('mode', $mode);
        $this->assign('banner', $banner);
        $this->assign('list', $list);
        $this->assign('model', $model);
        return $this->fetch();
    }


    public function step2($id)
    {
        $data = $this->request->get();

//        if(!session('users.uid')){
//            $this->error("请先登录",'/login');
//        }

        $model = Product::where(['status' => 1, 'id' => $id])->find();

        if (!$model) {
            $this->error("该商品已下架");
        }

        $custom = Product::where('id', $id)->find();
//        dump($custom);
        $this->assign('custom', $custom);
        $this->assign('banner', $data['banner']);
        $this->assign('marking', $data['marking']);
        $this->assign('model', $model);
        return $this->fetch();
    }

    public function step3($id, $address_id = 0)
    {
        $data = $this->request->get();
        $model = $model = Product::where(['status' => 1, 'id' => $id])->find();

        if (!$model) {
            $this->error("该商品已下架");
        }
        if ($address_id == 0) {
            $address = UsersAddress::get(['uid' => session('users.uid'), 'default' => 1, 'status' => 1]);
        } else {
            $address = UsersAddress::get($address_id);
        }
        if ($this->request->isPost()) {
            try {
                $data = $this->request->post();
                $banner = ProductTypeModel::get(['id' => $data['banner']]);
                $marking = ProductTypeModel::get(['id' => $data['marking']]);
                //写入订单
                $orders = OrdersModel::create([
                    'uid' => session('users.uid'),
                    'address' => json_encode($address),
                    'moneys' => bcmul($data['number'], $model['price'], 2) + 5000.00,
                    'state' => 2,
                    'banner' => $banner['title'],
                    'marking' => $marking['title'],
                ]);
                if (false === $orders) {
                    throw new \Exception("Err:100 下单失败！");
                }

                $detailSave = OrdersDetail::create([
                    'uid' => session('users.uid'),
                    'pid' => $id,
                    'price' => $model['price'],
                    'number' => $data['number'],
                    'orders_id' => $orders['id']
                ]);
                if (!$detailSave) {
                    throw new \Exception("Err:101 下单失败！");
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->errorResult($e->getMessage(), $e->getCode());
            }
            $this->successResult(['id' => $orders['id']]);
        }


        $this->assign('address', $address);
        $this->assign('banner', $data['banner']);
        $this->assign('marking', $data['marking']);
        $this->assign('model', $model);

        return $this->fetch();
    }

    /**
     * @param $id
     * @param int $mode
     * @return mixed
     * @throws \Exception
     */
    public function step4($id, $mode = 0)
    {
        $data = $this->request->get();
        session('settlement_url', null);


        $model = Orders::where(['a.id' => $id, 'a.uid' => session('users.uid')])
            ->alias('a')
            ->field('a.*,b.nickname,b.telephone')
            ->leftJoin('users b', 'a.uid = b.uid')
            ->find();
        $invoice = @json_decode($model['invoice']);


        $address = json_decode($model['address'], true);
        $this->assign(compact('model', 'id', 'mode', 'address', 'invoice'));
        $this->assign('banner', $data['banner']);
        $this->assign('marking', $data['marking']);
        return $this->fetch();
    }

    public function step5()
    {
        return $this->fetch();
    }

    //表面工艺及颜色
    public function tech()
    {
        $data = $this->request->get();
        $model = Product::alias("a")
            ->leftJoin('admin_attachment b', 'a.cover = b.id')
            ->field('a.*,b.path')
            ->where('a.id', $data['id'])
            ->find();

        if (!$model) {
            $this->errorResult("参数传递错误");
        }

        $this->successResult($model);
    }

}