<?php
/*
 * @link  https://www.xaiznhou.cn
 * @author xiaomin<keacefull@gmail.com>
 * @copyright 2014-2019 Xianzhou Technology (Chongqing) LLC.
 */

namespace app\common\model;


use think\Model;

class Users extends Model
{
    protected $pk = 'uid';

    public $autoWriteTimestamp = true;

    public function getTypeAttr($value){
        return self::getTypeData()[$value] ?? '个人';
    }


    public static function getTypeData(){
        return [
            0 => '个人',
            1 => '车企',
            2 => '经销商',
        ];
    }


    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    public function verifyPassword($password){
        return password_verify($password,$this->password);
    }
}