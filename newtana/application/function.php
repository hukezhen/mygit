<?php
/**
 * 加密
 */
if(!function_exists('des3_encrypt')){
    function des3_encrypt($str,$des_key="",$des_iv="")
    {
        return base64_encode(openssl_encrypt($str, 'des-ede3-cbc', $des_key, OPENSSL_RAW_DATA, $des_iv));
    }
}