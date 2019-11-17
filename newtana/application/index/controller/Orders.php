<?php


namespace app\index\controller;


use app\common\model\Orders as OrdersModel;
use app\common\model\OrdersDetail;
use app\common\model\Product;
use app\common\model\ShopCar;
use app\common\model\UsersAddress;
use think\Db;

class Orders extends Home
{
    /**
     * 订单列表
     * @param int $id
     * @param int $status
     * @param int $state
     * @return mixed
     * @throws \Exception
     */
    public function index($id = 0, $status = 0, $state = 1)
    {

        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $update = OrdersModel::where('id', $id)->update(['status' => 4]);
            if (false === $update) {
                $this->error('确认收货失败！');
            } else {
                $this->success('操作成功');
            }
        }
        $query = OrdersModel::with('detail')
            ->where('a.uid', session('users.uid'))
            ->alias('a')
            ->leftJoin('users g', 'g.uid=a.uid')
            ->field([
                'a.*',
                'g.nickname,g.telephone'
            ]);
        $listQuery = clone $query;
        if ($status != 0) {
            $listQuery->where('a.status', $status);
        }
        if ($state == 2) {
            $listQuery->where(['a.state' => $state]);
        }
        $list = $listQuery->order('a.update_time', 'desc')->select()->toArray();
        if ($id == 0) {
            !empty($list) && $id = $list[0]['id'];
        }
        $order = $query->where('a.id', $id)->find();
        $this->assign(compact('list', 'order', 'id', 'status', 'state'));
        return $this->fetch();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function read($id)
    {
        if ($this->request->isPost()) {
            $update = OrdersModel::where('id', $id)->update(['status' => 4]);
            if (false === $update) {
                $this->error('确认收货失败！');
            } else {
                $this->success('操作成功');
            }
        }

        $order = OrdersModel::with('detail')
            ->where('a.uid', session('users.uid'))
            ->alias('a')
            ->leftJoin('users g', 'g.uid=a.uid')
            ->field([
                'a.*',
                'g.nickname,g.telephone'
            ])
            ->where('a.id', $id)
            ->find();
        $this->assign(compact('order', 'id'));
        return $this->fetch();
    }

    /**
     * 创建订单
     * @throws \Exception
     */
    public function save()
    {
        $data = $this->request->post();

        if (!isset($data['id'])) {
            $this->errorResult("参数传递错误");
        }

        $id = explode(',', substr($data['id'], 0, -1));
        $num = explode(',', substr($data['num'], 0, -1));
        $sid = explode(',', substr($data['sid'], 0, -1));


        $pid = implode(",", $id);

        Db::startTrans();
        try {
            $address = UsersAddress::get(['uid' => session('users.uid'), 'default' => 1]);

            if (!$address) {
                throw new \Exception("您还没有添加收货地址！", -1);
            }

            $address = UsersAddress::get(['uid' => session('users.uid'), 'default' => 1]);
            //写入订单
            $orders = OrdersModel::create([
                'uid' => session('users.uid'),
                'address' => json_encode($address)
            ]);
            if (false === $orders) {
                throw new \Exception("Err:100 下单失败！");
            }
            $product = Product::where('id', 'in', $pid)->field('id,price')->select();
            $car = [];
            foreach ($num as $key => $val) {
                $car[$id[$key]] = $val;
            }
            $detail = [];
            $money = 0;
            foreach ($product as $key => $val) {
                foreach ($car as $k => $v) {
                    if ($val['id'] == $k) {
                        //计算价格
                        $money += $val['price'] * $v;
                        $detail[] = [
                            'uid' => session('users.uid'),
                            'pid' => $val['id'],
                            'price' => $val['price'],
                            'number' => $v,
                            'orders_id' => $orders['id']
                        ];
                    }
                }
            }
            $detailSave = (new OrdersDetail())->saveAll($detail);
            if (!$detailSave) {
                throw new \Exception("Err:101 下单失败！");
            }
            //修改订单金额
            $ordersUpdate = OrdersModel::update(['id' => $orders['id'], 'moneys' => $money]);
            if (false === $ordersUpdate) {
                throw new \Exception("Err:102 下单失败！");
            }
            //删除购物车
            $delCar = ShopCar::destroy($sid);
            if (false === $delCar) {
                throw new \Exception("Err:103 下单失败！");
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->errorResult($e->getMessage(), $e->getCode());
        }

        $this->successResult(['id' => $orders['id']], '提交成功');

    }


    /**
     * 订单支付
     * @param $id
     * @param int $mode
     * @param int $address_id
     * @return mixed
     * @throws \Exception
     */
    public function payment($id, $mode = 0, $address_id = 0)
    {
        session('settlement_url', null);
        if ($this->request->isPost()) {
            $update = OrdersModel::where('id', $id)->update(['status' => 4]);
            if (false === $update) {
                $this->error('确认收货失败！');
            } else {
                $this->success('操作成功');
            }
        }
        $order = OrdersModel::with('detail')
            ->where('a.uid', session('users.uid'))
            ->alias('a')
            ->leftJoin('users g', 'g.uid=a.uid')
            ->field([
                'a.*',
                'g.nickname,g.telephone'
            ])
            ->where('a.id', $id)
            ->find();
        if ($address_id > 0) {
            $address = UsersAddress::get($address_id);
            OrdersModel::update(['id' => $id, 'address' => json_encode($address)]);
        } else {
            $address = json_decode($order['address'], true);
        }
        $invoice = @json_decode($order['invoice'], true);

        if ($order['status'] != 1) {
            $this->error('该订单暂时不能支付！', '/orders.html');
        }

        $this->assign(compact('order', 'id', 'mode', 'address', 'invoice'));
        return $this->fetch();
    }

    /**
     * 保存发票
     * @param $id
     */
    public function invoice($id)
    {
        OrdersModel::update(['id' => $id, 'invoice' => json_encode($this->request->post())]);
        $this->successResult(['status' => true]);
    }
    public function txt($id)
    {
        OrdersModel::update(['id' => $id, 'txt' => $this->request->post('txt')]);
        $this->successResult(['status' => true]);
    }
}
