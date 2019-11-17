<?php


namespace app\index\controller;


use app\common\model\Product;
use app\common\model\ShopCar;

class Cart extends Home
{
    /**
     * 购物车列表
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {
        $model = ShopCar::alias("a")
            ->leftJoin('product b', 'a.goods_id = b.id')
            ->field("a.id,a.goods_id,a.number,b.part_number,b.price,b.cover")
            ->where(['a.uid' => session('users.uid'), 'a.status' => 1])
            ->select();

        $price = 0;
        foreach ($model as $key => $val) {
            $price = $price + $val['price'] * $val['number'];
        }
        $this->assign('number', count($model));
        $this->assign('price', sprintf("%.2f", $price));
        $this->assign('model', $model);
        return $this->fetch();
    }

    /**
     * 加入购物车
     * @throws \Exception
     */
    public function save()
    {
        $data = $this->request->post();
        if (empty($data['id'])) {
            $this->errorResult("参数传递错误101");
        }

        $goods = Product::where(['status' => 1, 'id' => $data['id']])->find();
        if (!$goods) {
            $this->errorResult("参数传递错误201");
        }

        $shop = ShopCar::get(['goods_id' => $goods['id'], 'uid' => session("users.uid")]);
        if ($shop) {
            $shop->number = ['inc', $data['number']];
            $model = $shop->save();
        } else {
            $model = ShopCar::create([
                'uid' => session('users.uid'),
                'goods_id' => $goods['id'],
                'number' => $data['number']
            ]);
        }
        if (false === $model) {
            $this->errorResult('加入购物车失败');
        }
        $cartNumber = ShopCar::where('uid', session('users.uid'))->count();
        $this->successResult(['number'=>$cartNumber], "加入购物车成功");
    }

    /**
     * 删除购物车
     * @throws \Exception
     */
    public function delete()
    {
        $data = $this->request->post();

        $model = ShopCar::where('uid', session('users.uid'))->where('goods_id', 'in', $data['id'])->delete();
        if (false === $model) {
            $this->errorResult("删除失败");
        } else {
            $this->successResult([], "删除成功");
        }
    }
}