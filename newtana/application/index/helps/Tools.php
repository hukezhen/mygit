<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\index\helps;


use app\common\model\EmailRecord;
use Exception;
use think\Db;
use app\common\model\SmsRecord;

class Tools
{

    /**
     * 发送短信验证码
     * @param string $telephone
     * @param string $code
     * @param string $nationCode
     * @param int $scene
     * @return bool
     */
    public static function sendSmsCode(string $telephone, string $code, string $nationCode = '86', int $scene = 0)
    {

        Db::startTrans();
        try {
            //限制短信发送频率
            $smsRecord = SmsRecord::get([
                'telephone' => $telephone,
                'nation_code' => $nationCode,
                'scene' => $scene
            ]);

            if ($smsRecord && $smsRecord['create_time'] + 60 > time()) {
                throw new \Exception('请一分钟后再试！');
            }
            //写入发送数据
            $codeData = [
                'telephone' => $telephone,
                'code' => $code,
                'nation_code' => $nationCode,
                'scene' => $scene
            ];
            if (!SmsRecord::create($codeData, true)) {
                throw new \Exception('数据写入失败！');
            }

            //发送验证码
            $body = [
                'telephone' => $telephone,
                'nationCode' => $nationCode,
                'content' => sprintf(config('sms_tpl'), $code)
            ];

            $result = plugin_action('SendSms/Sms/send', [$body]);

            if (false === $result) {
                throw new \Exception('短信验证码发送失败');
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**
     * 发送邮件验证码
     * @param string $code
     * @param string $email
     * @param string $name
     * @return bool
     */
    public static function sendEmailCode(string $email, string $code, string $name = '', $scene = 0)
    {
        Db::startTrans();
        try {
            $codeData = [
                'email' => $email,
                'code' => $code,
                'scene' => $scene
            ];
            if (!EmailRecord::create($codeData, true)) {
                throw new Exception('数据写入失败！');
            }
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = config('smtp_host');
            $mail->SMTPAuth = true;
            $mail->Username = config('smtp_username');
            $mail->Password = config('smtp_password');
            $mail->SMTPSecure = config('smtp_secure');
            $mail->Port = config('smtp_port');
            $mail->setFrom(config('smtp_from'), config('smtp_from_username'));
            $mail->addAddress($email, $name ?? config('smtp_to_name'));
            $mail->isHTML(true);
            $mail->Subject = config('smtp_title');
            $mail->Body = sprintf(config('smtp_body'), $code);


            if (false === $mail->send()) {
                throw new \Exception('验证码发送失败');
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return true;
    }

    /**
     * 生成订单号
     * @param int $uid
     * @return string
     */
    public static function getOrdersSn(int $uid)
    {
        return strtoupper(md5($uid . time() . rand() . rand()));
    }

    /**
     * 生成支付单号
     * @param int $uid
     * @param int $id
     * @param string $type
     * @return string
     */
    public static function getOutTradeNo(int $uid, int $id, string $type)
    {
        return $type . date('YmdHis') . $uid  . rand();
    }


    public static function freight($code, $logistic)
    {
        $appId = config('kdniao_appid');
        $appKey = config('kdniao_appkey');

        $requestData = "{'OrderCode':'','ShipperCode':'{$code}','LogisticCode':'{$logistic}'}";

        $data = array(
            'EBusinessID' => $appId,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );
        $data['DataSign'] = urlencode(base64_encode(md5($requestData . $appKey)));;
        $curl = new Curl();

        $result = $curl->post('http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx', $data);
        return json_decode($result, true);
    }


}