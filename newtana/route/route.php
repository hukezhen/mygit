<?php

use think\facade\Route;

Route::rule('/', 'index/index/index');
Route::rule('hub', 'index/index/hub');
Route::rule('detail', 'index/index/detail');
Route::rule('gallery', 'index/index/gallery');
Route::rule('gallery_detail', 'index/index/gallery_detail');
Route::rule('craft_detail', 'index/index/craft_detail');
Route::rule('craft', 'index/index/craft');
//联系我们
Route::rule('contact', 'index/index/contact');
//退出登录
Route::rule('logout', 'index/index/logout');
//订单
Route::rule('orders', 'index/orders/index');
//订单详情
Route::rule('orders_detail', 'index/orders/read');
//订单支付
Route::rule('payment', 'index/orders/payment');
//购物车列表
Route::rule('cart', 'index/cart/index');
//地址列表
Route::rule('modify_address', 'index/address/index');
//添加地址
Route::rule('address_create', 'index/address/save');
//登录
Route::rule('login', 'index/auth/login');
//注册
Route::rule('register', 'index/auth/register');
//找回密码
Route::rule('retrieve', 'index/auth/retrieve');
//手机版注册
Route::rule('web_register', 'index/auth/web_register');
//我的资料
Route::rule('personal_data', 'index/personal/index');
//修改资料
Route::rule('personal_data_update', 'index/personal/update');


Route::rule('custom/index', 'index/custom/index');
Route::rule('custom/step1', 'index/custom/step1');
Route::rule('custom/step2', 'index/custom/step2');
Route::rule('custom/step3', 'index/custom/step3');
Route::rule('custom/step4', 'index/custom/step4');
Route::rule('custom/step5', 'index/custom/step5');
Route::rule('custom/tech', 'index/custom/tech');