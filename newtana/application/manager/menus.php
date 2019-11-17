<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

/**
 * 菜单信息
 */
return [
  [
    'title' => '综合管理',
    'icon' => 'fa fa-fw fa-wrench',
    'url_type' => 'module_admin',
    'url_value' => 'manager/index/index',
    'url_target' => '_self',
    'online_hide' => 0,
    'sort' => 100,
    'status' => 1,
    'child' => [
      [
        'title' => '控制台',
        'icon' => 'fa fa-fw fa-dashboard',
        'url_type' => 'module_admin',
        'url_value' => 'manager/index/index',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
      ],
    ],
  ],
];
