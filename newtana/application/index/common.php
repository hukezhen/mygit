<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

if (!function_exists('sendSmsCode')) {
    /**
     * 发送短信验证码
     *
     * @param string $telephone
     * @param string $code
     * @param string $expires
     * @param string $nationCode
     * @param int $scene
     *
     * @return mixed
     */
    function sendSmsCode(string $telephone, int $scene, string $code, int $status = 2, string $expires = '5', string $nationCode = '86')
    {
        \think\Db::startTrans();
        try {
            //限制短信发送频率
            $smsRecord = \app\common\model\SmsRecord::where('telephone', $telephone)
                ->where('nation_code', $nationCode)
                ->where('scene', $scene)
                ->order('create_time', 'desc')
                ->find();

            if ($smsRecord && $smsRecord['create_time'] + 60 > time()) {
                throw new \Exception('请一分钟后再试！');
            }
            //写入发送数据
            $codeData = [
                'telephone' => $telephone,
                'code' => $code,
                'nation_code' => $nationCode,
                'scene' => $scene,
            ];
            if (false === $smsRecord = \app\common\model\SmsRecord::create($codeData, true)) {
                throw new \Exception('数据写入失败！');
            }

            //发送验证码
            $body = [
                'telephone' => $telephone,
                'nationCode' => $nationCode,
                'template' => '9000001563372611',
                'data' => ['code' => $code]
            ];

            $result = plugin_action('SendSms/Sms/send', [$body]);
            if (false === $result) {
                throw new \Exception('短信验证码发送失败');
            }
            \think\Db::commit();
        } catch (\Exception $e) {
            \think\Db::rollback();

            return $e->getMessage();
        }

        return ['status' => true, 'id' => $smsRecord['id']];
    }
}

if (!function_exists('shortMd5')) {
    /**
     * 返回16位md5值
     *
     * @param string $str 字符串
     *
     * @return string $str 返回16位的字符串
     */
    function shortMd5(string $str)
    {
        return substr(md5($str), 8, 16);
    }
}

if (!function_exists('')) {
    function generateOrdersSn($uid)
    {
        return date('YmdHis') . $uid . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('downloadWeChatPic')) {
    /**
     * 微信下载图片.
     *
     * @param string $url
     *
     * @return bool|string
     */
    function downloadWeChatPic(string $url)
    {
        $header = [
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
            'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip, deflate',
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($code == 200) {
            //把URL格式的图片转成base64_encode格式的！
            $imgBase64Code = 'data:image/jpeg;base64,' . base64_encode($data);
            $img_content = $imgBase64Code; //图片内容
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)) {
                $type = $result[2];
            } //得到图片类型png?jpg?gif?
            $filePath = \think\facade\Env::get('root_path') . 'public/uploads/images/' . date('Ymd') . '/';
            if (!is_dir($filePath)) {
                mkdir($filePath, '0755');
            }
            $fileName = 'face-' . time() . rand(1, 10000) . ".{$type}";

            $new_file = $filePath . $fileName;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img_content)))) {
                return '/uploads/images/' . date('Ymd') . '/' . $fileName;
            }
        }

        return false;
    }
}

if (!function_exists('generateTree')) {
    /**
     * 处理三级联动包.
     *
     * @param array $items
     * @param string $parentKey
     *
     * @return array
     */
    function generateTree(array $items, string $parentKey = 'pid')
    {
        $tree = $temp = [];
        foreach ($items as $item) {
            $temp[$item['id']] = $item;
        }
        foreach ($items as $item) {
            if (isset($temp[$item[$parentKey]])) {
                $temp[$item[$parentKey]]['sub'][] = &$temp[$item['id']];
            } else {
                $tree[] = &$temp[$item['id']];
            }
            // 下面可以去掉 pid 元素
            unset($temp[$item['id']][$parentKey]);
        }

        return $tree;
    }
}


if (!function_exists('sendMsg')) {

    /**
     * 发送消息给前端用户
     * @param string $content
     * @param $uids
     * @param int $sendUid
     * @return bool
     * @throws Exception
     */
    function sendMsg(string $content, $uids, int $sendUid = 0)
    {
        $uids = is_array($uids) ? $uids : explode(',', $uids);
        $list = [];
        foreach ($uids as $uid) {
            $list[] = [
                'uid_receive' => $uid,
                'uid_send' => $sendUid,
                'content' => $content,
            ];
        }

        $MessageModel = model('shop/message');
        return false !== $MessageModel->saveAll($list);
    }
}
