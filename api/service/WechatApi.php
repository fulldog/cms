<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/7
 * Time: 0:15
 */

namespace api\service;

class WechatApi
{
    private $loginApi = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
    private $appid = 'wx5116334d09656bc8';
    private $appsecret = '2b852f1b7915a04704a2382712026f7c';
    private $sessionKey;

    /**
     * 构造函数
     * @param $appid string 小程序的appid
     * @param $appsecret string $appsecret
     */
    public function __construct($appid = '', $appsecret = '')
    {
//        $this->sessionKey = $sessionKey;
        $this->appid = $appid ?: $this->appid;
        $this->appsecret = $appsecret ?: $this->appsecret;
    }

    /**
     * @param $code
     * @return array
     */
    public function getOpenByCode($code)
    {
        $this->loginApi = sprintf($this->loginApi, $this->appid, $this->appsecret, $code);
        return json_decode(file_get_contents($this->loginApi), true) ?: [];
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功1，失败返回对应的错误码
     */
    public function decryptData($encryptedData, $iv, &$data)
    {
        if (strlen($this->sessionKey) != 24) {
            return "sessionKey error";
        }
        $aesKey = base64_decode($this->sessionKey);

        if (strlen($iv) != 24) {
            return "iv error";
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return json_last_error();
        }
        if ($dataObj->watermark->appid != $this->appid) {
            return "appid error";
        }
        $data = $result;
        return true;
    }

}