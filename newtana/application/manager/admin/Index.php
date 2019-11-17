<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\manager\admin;


use app\admin\controller\Admin;
use app\common\model\Users as UsersModel;
use app\common\model\Browse as BrowseModel;

class Index extends Admin
{

    public function index()
    {
        //用户总数
        $userCount = UsersModel::count('uid');
        //今日注册数
        $todayUsersCount = UsersModel::whereTime('create_time', 'today')->count('uid');
        //用户总数
        $browseCount = BrowseModel::count('id');
        //今日注册数
        $todayBrowseCount = BrowseModel::whereTime('create_time', 'today')->count('id');
        //获取最近7天
        $userTemp = [];
        $browseTemp = [];
        for ($i = 0; $i < 7; $i++) {
            $userTemp[] = [
                'days' => date('Y-m-d', strtotime("-$i day")),
                'usersNumber' => 0
            ];
            $browseTemp[] = [
                'days' => date('Y-m-d', strtotime("-$i day")),
                'number' => 0
            ];
            $week[] = date('y/m/d', strtotime("-$i day"));
        }
        $week = json_encode(array_reverse($week), JSON_UNESCAPED_SLASHES);

        //7天用户注册数据
        $list = UsersModel::whereTime('create_time', [date('Y-m-d', strtotime('-7 day')), date('Y-m-d', strtotime('+1 day'))])
            ->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as  days,count(uid) as usersNumber")->group('days')->select();
        foreach ($list as $k => $v) {
            foreach ($userTemp as $kk => $vv) {
                if ($vv['days'] == $v['days']) {
                    $userTemp[$kk]['usersNumber'] = $v['usersNumber'];
                }
            }
        }
        foreach ($userTemp as $kk => $vv) {
            $userNumber[] = $vv['usersNumber'];
        }

        $userNumber = json_encode(array_reverse($userNumber), JSON_UNESCAPED_SLASHES);
        //7天浏览数据
        $list = BrowseModel::whereTime('create_time', [date('Y-m-d', strtotime('-7 day')), date('Y-m-d', strtotime('+1 day'))])
            ->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as  days,count(id) as number")->group('days')->select();
        foreach ($list as $k => $v) {
            foreach ($browseTemp as $kk => $vv) {
                if ($vv['days'] == $v['days']) {
                    $userTemp[$kk]['number'] = $v['number'];
                }
            }
        }
        foreach ($browseTemp as $kk => $vv) {
            $browseNumber[] = $vv['number'];
        }
        $browseNumber = json_encode(array_reverse($browseNumber), JSON_UNESCAPED_SLASHES);

        $this->assign(compact(
            'userCount',
            'todayUsersCount',
            'browseCount',
            'todayBrowseCount',
            'userNumber',
            'browseNumber',
            'week'
        ));
        return $this->fetch();
    }

}